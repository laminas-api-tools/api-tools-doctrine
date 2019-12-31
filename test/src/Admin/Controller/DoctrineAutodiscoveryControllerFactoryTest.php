<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-doctrine for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\Doctrine\Admin\Controller;

use Interop\Container\ContainerInterface;
use Laminas\ApiTools\Doctrine\Admin\Controller\DoctrineAutodiscoveryController;
use Laminas\ApiTools\Doctrine\Admin\Controller\DoctrineAutodiscoveryControllerFactory;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineAutodiscoveryModel;
use Laminas\ServiceManager\AbstractPluginManager;
use Prophecy\Prophecy\ProphecyInterface;

class DoctrineAutodiscoveryControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ProphecyInterface|ContainerInterface
     */
    private $container;

    /**
     * @var DoctrineAutodiscoveryModel
     */
    private $model;

    protected function setUp()
    {
        parent::setUp();

        $this->model = $this->prophesize(DoctrineAutodiscoveryModel::class)->reveal();
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->container->get(DoctrineAutodiscoveryModel::class)->willReturn($this->model);
    }

    public function testInvokableFactoryReturnsDoctrineAutodiscoveryController()
    {
        $factory = new DoctrineAutodiscoveryControllerFactory();
        $controller = $factory($this->container->reveal(), DoctrineAutodiscoveryController::class);

        $this->assertInstanceOf(DoctrineAutodiscoveryController::class, $controller);
        $this->assertAttributeSame($this->model, 'model', $controller);
    }

    public function testLegacyFactoryReturnsDoctrineAutodiscoveryController()
    {
        $controllers = $this->prophesize(AbstractPluginManager::class);
        $controllers->getServiceLocator()->willReturn($this->container->reveal());

        $factory = new DoctrineAutodiscoveryControllerFactory();
        $controller = $factory->createService($controllers->reveal());

        $this->assertInstanceOf(DoctrineAutodiscoveryController::class, $controller);
        $this->assertAttributeSame($this->model, 'model', $controller);
    }
}
