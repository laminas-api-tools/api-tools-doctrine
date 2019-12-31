<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-doctrine for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\Doctrine\Admin\Model;

use Interop\Container\ContainerInterface;
use Laminas\ApiTools\Admin\Model\DocumentationModel;
use Laminas\ApiTools\Admin\Model\InputFilterModel;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRpcServiceModelFactory;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRpcServiceResource;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRpcServiceResourceFactory;
use Laminas\Mvc\Controller\ControllerManager;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Prophecy\Prophecy\ProphecyInterface;

class DoctrineRpcServiceResourceFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ProphecyInterface|ContainerInterface
     */
    private $container;

    protected function setUp()
    {
        parent::setUp();

        $this->container = $this->prophesize(ContainerInterface::class);
    }

    public function missingDependencies()
    {
        return [
            'all' => [[
                DoctrineRpcServiceModelFactory::class => false,
                InputFilterModel::class => false,
                'ControllerManager' => false,
                DocumentationModel::class => false,
            ]],
            'DoctrineRpcServiceModelFactory' => [[
                DoctrineRpcServiceModelFactory::class => false,
                InputFilterModel::class => true,
                'ControllerManager' => true,
                DocumentationModel::class => true,
            ]],
            'InputFilterModel' => [[
                DoctrineRpcServiceModelFactory::class => true,
                InputFilterModel::class => false,
                'ControllerManager' => true,
                DocumentationModel::class => true,
            ]],
            'ControllerManager' => [[
                DoctrineRpcServiceModelFactory::class => true,
                InputFilterModel::class => true,
                'ControllerManager' => false,
                DocumentationModel::class => true,
            ]],
            'DocumentationModel' => [[
                DoctrineRpcServiceModelFactory::class => true,
                InputFilterModel::class => true,
                'ControllerManager' => true,
                DocumentationModel::class => false,
            ]],
        ];
    }

    /**
     * @dataProvider missingDependencies
     *
     * @var array $dependencies
     */
    public function testFactoryRaisesExceptionIfDependenciesAreMissing($dependencies)
    {
        $factory = new DoctrineRpcServiceResourceFactory();

        foreach ($dependencies as $dependency => $presence) {
            $this->container->has($dependency)->willReturn($presence);
        }

        $this->setExpectedException(ServiceNotCreatedException::class, 'missing one or more dependencies');
        $factory($this->container->reveal());
    }

    public function testFactoryReturnsConfiguredDoctrineRpcServiceResource()
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
