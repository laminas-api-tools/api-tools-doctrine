<?php

declare(strict_types=1);

namespace LaminasTest\ApiTools\Doctrine\Admin\Model;

use Laminas\ApiTools\Admin\Model\ModuleModel;
use Laminas\ApiTools\Admin\Model\ModulePathSpec;
use Laminas\ApiTools\Configuration\ConfigResourceFactory;
use Laminas\ApiTools\Configuration\ResourceFactory;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceModel;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceModelFactory;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceModelFactoryFactory;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\ServiceManager;
use LaminasTest\ApiTools\Doctrine\DeprecatedAssertionsTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ProphecyInterface;

class DoctrineRestServiceModelFactoryFactoryTest extends TestCase
{
    use DeprecatedAssertionsTrait;
    use ProphecyTrait;

    /** @var ProphecyInterface|ServiceManager */
    private $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = $this->prophesize(ServiceManager::class);
    }

    /** @psalm-return array<string, array{0: array<non-empty-string|class-string, bool>}> */
    public function missingDependencies(): array
    {
        return [
            'all'                   => [
                [
                    ModulePathSpec::class        => false,
                    ConfigResourceFactory::class => false,
                    ModuleModel::class           => false,
                    'SharedEventManager'         => false,
                ],
            ],
            'ModulePathSpec'        => [
                [
                    ModulePathSpec::class        => false,
                    ConfigResourceFactory::class => true,
                    ModuleModel::class           => true,
                    'SharedEventManager'         => true,
                ],
            ],
            'ConfigResourceFactory' => [
                [
                    ModulePathSpec::class        => true,
                    ConfigResourceFactory::class => false,
                    ModuleModel::class           => true,
                    'SharedEventManager'         => true,
                ],
            ],
            'ModuleModel'           => [
                [
                    ModulePathSpec::class        => true,
                    ConfigResourceFactory::class => true,
                    ModuleModel::class           => false,
                    'SharedEventManager'         => true,
                ],
            ],
            'SharedEventManager'    => [
                [
                    ModulePathSpec::class        => true,
                    ConfigResourceFactory::class => true,
                    ModuleModel::class           => true,
                    'SharedEventManager'         => false,
                ],
            ],
        ];
    }

    /**
     * @dataProvider missingDependencies
     */
    public function testFactoryRaisesExceptionIfDependenciesAreMissing(array $dependencies)
    {
        $factory = new DoctrineRestServiceModelFactoryFactory();

        foreach ($dependencies as $dependency => $presence) {
            $this->container->has($dependency)->willReturn($presence);
        }

        $this->expectException(ServiceNotCreatedException::class);
        $this->expectExceptionMessage('missing one or more dependencies');
        $factory($this->container->reveal());
    }

    public function testFactoryReturnsConfiguredDoctrineRestServiceModelFactory()
    {
        $factory               = new DoctrineRestServiceModelFactoryFactory();
        $pathSpec              = $this->prophesize(ModulePathSpec::class)->reveal();
        $configResourceFactory = $this->prophesize(ResourceFactory::class)->reveal();
        $moduleModel           = $this->prophesize(ModuleModel::class)->reveal();
        $sharedEvents          = $this->prophesize(SharedEventManagerInterface::class);

        $sharedEvents->attach(
            DoctrineRestServiceModel::class,
            'fetch',
            [DoctrineRestServiceModel::class, 'onFetch']
        )->shouldBeCalled();

        $this->container->has(ModulePathSpec::class)->willReturn(true);
        $this->container->has(ConfigResourceFactory::class)->willReturn(true);
        $this->container->has(ModuleModel::class)->willReturn(true);
        $this->container->has('SharedEventManager')->willReturn(true);

        $this->container->get(ModulePathSpec::class)->willReturn($pathSpec);
        $this->container->get(ConfigResourceFactory::class)->willReturn($configResourceFactory);
        $this->container->get(ModuleModel::class)->willReturn($moduleModel);
        $this->container->get('SharedEventManager')->willReturn($sharedEvents->reveal());

        $restFactory = $factory($this->container->reveal());

        $this->assertInstanceOf(DoctrineRestServiceModelFactory::class, $restFactory);
        $this->assertAttributeSame($pathSpec, 'modules', $restFactory);
        $this->assertAttributeSame($configResourceFactory, 'configFactory', $restFactory);
        $this->assertAttributeSame($moduleModel, 'moduleModel', $restFactory);
        $this->assertAttributeSame($sharedEvents->reveal(), 'sharedEventManager', $restFactory);
    }
}
