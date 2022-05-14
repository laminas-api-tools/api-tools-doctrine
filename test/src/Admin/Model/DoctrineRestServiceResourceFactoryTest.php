<?php

declare(strict_types=1);

namespace LaminasTest\ApiTools\Doctrine\Admin\Model;

use interop\container\containerinterface;
use Laminas\ApiTools\Admin\Model\DocumentationModel;
use Laminas\ApiTools\Admin\Model\InputFilterModel;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceModelFactory;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceResource;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceResourceFactory;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use LaminasTest\ApiTools\Doctrine\DeprecatedAssertionsTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ProphecyInterface;

class DoctrineRestServiceResourceFactoryTest extends TestCase
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

    /**
     * @return bool[][][]
     * @psalm-return array<string, array{
     *     0: array<class-string, bool>
     * }>
     */
    public function missingDependencies(): array
    {
        return [
            'all'                             => [
                [
                    DoctrineRestServiceModelFactory::class => false,
                    InputFilterModel::class                => false,
                    DocumentationModel::class              => false,
                ],
            ],
            'DoctrineRestServiceModelFactory' => [
                [
                    DoctrineRestServiceModelFactory::class => false,
                    InputFilterModel::class                => true,
                    DocumentationModel::class              => true,
                ],
            ],
            'InputFilterModel'                => [
                [
                    DoctrineRestServiceModelFactory::class => true,
                    InputFilterModel::class                => false,
                    DocumentationModel::class              => true,
                ],
            ],
            'DocumentationModel'              => [
                [
                    DoctrineRestServiceModelFactory::class => true,
                    InputFilterModel::class                => true,
                    DocumentationModel::class              => false,
                ],
            ],
        ];
    }

    /**
     * @dataProvider missingDependencies
     */
    public function testFactoryRaisesExceptionIfDependenciesAreMissing(array $dependencies): void
    {
        $factory = new DoctrineRestServiceResourceFactory();

        foreach ($dependencies as $dependency => $presence) {
            $this->container->has($dependency)->willReturn($presence);
        }

        $this->expectException(ServiceNotCreatedException::class);
        $this->expectExceptionMessage('missing one or more dependencies');
        $factory($this->container->reveal());
    }

    public function testFactoryReturnsConfiguredDoctrineRestServiceResource(): void
    {
        $factory            = new DoctrineRestServiceResourceFactory();
        $restFactory        = $this->prophesize(DoctrineRestServiceModelFactory::class)->reveal();
        $inputFilterModel   = $this->prophesize(InputFilterModel::class)->reveal();
        $documentationModel = $this->prophesize(DocumentationModel::class)->reveal();

        $this->container->has(DoctrineRestServiceModelFactory::class)->willReturn(true);
        $this->container->has(InputFilterModel::class)->willReturn(true);
        $this->container->has(DocumentationModel::class)->willReturn(true);

        $this->container->get(DoctrineRestServiceModelFactory::class)->willReturn($restFactory);
        $this->container->get(InputFilterModel::class)->willReturn($inputFilterModel);
        $this->container->get(DocumentationModel::class)->willReturn($documentationModel);

        $resource = $factory($this->container->reveal());

        $this->assertInstanceOf(DoctrineRestServiceResource::class, $resource);
        $this->assertAttributeSame($restFactory, 'restFactory', $resource);
        $this->assertAttributeSame($inputFilterModel, 'inputFilterModel', $resource);
        $this->assertAttributeSame($documentationModel, 'documentationModel', $resource);
    }
}
