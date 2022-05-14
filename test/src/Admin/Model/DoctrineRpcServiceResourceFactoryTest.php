<?php

declare(strict_types=1);

namespace LaminasTest\ApiTools\Doctrine\Admin\Model;

use interop\container\containerinterface;
use Laminas\ApiTools\Admin\Model\DocumentationModel;
use Laminas\ApiTools\Admin\Model\InputFilterModel;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRpcServiceModelFactory;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRpcServiceResource;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRpcServiceResourceFactory;
use Laminas\Mvc\Controller\ControllerManager;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use LaminasTest\ApiTools\Doctrine\DeprecatedAssertionsTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ProphecyInterface;

class DoctrineRpcServiceResourceFactoryTest extends TestCase
{
    use DeprecatedAssertionsTrait;
    use ProphecyTrait;

    /** @var ProphecyInterface|containerinterface */
    private $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = $this->prophesize(containerinterface::class);
    }

    /** @psalm-return array<string, array{0: array<non-empty-string|class-string, bool>}> */
    public function missingDependencies(): array
    {
        return [
            'all'                            => [
                [
                    DoctrineRpcServiceModelFactory::class => false,
                    InputFilterModel::class               => false,
                    'ControllerManager'                   => false,
                    DocumentationModel::class             => false,
                ],
            ],
            'DoctrineRpcServiceModelFactory' => [
                [
                    DoctrineRpcServiceModelFactory::class => false,
                    InputFilterModel::class               => true,
                    'ControllerManager'                   => true,
                    DocumentationModel::class             => true,
                ],
            ],
            'InputFilterModel'               => [
                [
                    DoctrineRpcServiceModelFactory::class => true,
                    InputFilterModel::class               => false,
                    'ControllerManager'                   => true,
                    DocumentationModel::class             => true,
                ],
            ],
            'ControllerManager'              => [
                [
                    DoctrineRpcServiceModelFactory::class => true,
                    InputFilterModel::class               => true,
                    'ControllerManager'                   => false,
                    DocumentationModel::class             => true,
                ],
            ],
            'DocumentationModel'             => [
                [
                    DoctrineRpcServiceModelFactory::class => true,
                    InputFilterModel::class               => true,
                    'ControllerManager'                   => true,
                    DocumentationModel::class             => false,
                ],
            ],
        ];
    }

    /**
     * @dataProvider missingDependencies
     */
    public function testFactoryRaisesExceptionIfDependenciesAreMissing(array $dependencies): void
    {
        $factory = new DoctrineRpcServiceResourceFactory();

        foreach ($dependencies as $dependency => $presence) {
            $this->container->has($dependency)->willReturn($presence);
        }

        $this->expectException(ServiceNotCreatedException::class);
        $this->expectExceptionMessage('missing one or more dependencies');
        $factory($this->container->reveal());
    }

    public function testFactoryReturnsConfiguredDoctrineRpcServiceResource(): void
    {
        $factory            = new DoctrineRpcServiceResourceFactory();
        $rpcFactory         = $this->prophesize(DoctrineRpcServiceModelFactory::class)->reveal();
        $inputFilterModel   = $this->prophesize(InputFilterModel::class)->reveal();
        $controllerManager  = $this->prophesize(ControllerManager::class)->reveal();
        $documentationModel = $this->prophesize(DocumentationModel::class)->reveal();

        $this->container->has(DoctrineRpcServiceModelFactory::class)->willReturn(true);
        $this->container->has(InputFilterModel::class)->willReturn(true);
        $this->container->has('ControllerManager')->willReturn(true);
        $this->container->has(DocumentationModel::class)->willReturn(true);

        $this->container->get(DoctrineRpcServiceModelFactory::class)->willReturn($rpcFactory);
        $this->container->get(InputFilterModel::class)->willReturn($inputFilterModel);
        $this->container->get('ControllerManager')->willReturn($controllerManager);
        $this->container->get(DocumentationModel::class)->willReturn($documentationModel);

        $resource = $factory($this->container->reveal());

        $this->assertInstanceOf(DoctrineRpcServiceResource::class, $resource);
        $this->assertAttributeSame($rpcFactory, 'rpcFactory', $resource);
        $this->assertAttributeSame($inputFilterModel, 'inputFilterModel', $resource);
        $this->assertAttributeSame($controllerManager, 'controllerManager', $resource);
        $this->assertAttributeSame($documentationModel, 'documentationModel', $resource);
    }
}
