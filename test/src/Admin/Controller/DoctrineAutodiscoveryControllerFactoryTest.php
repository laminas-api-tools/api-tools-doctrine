<?php

declare(strict_types=1);

namespace LaminasTest\ApiTools\Doctrine\Admin\Controller;

use Interop\Container\ContainerInterface;
use Laminas\ApiTools\Doctrine\Admin\Controller\DoctrineAutodiscoveryController;
use Laminas\ApiTools\Doctrine\Admin\Controller\DoctrineAutodiscoveryControllerFactory;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineAutodiscoveryModel;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LaminasTest\ApiTools\Doctrine\DeprecatedAssertionsTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ProphecyInterface;

class DoctrineAutodiscoveryControllerFactoryTest extends TestCase
{
    use DeprecatedAssertionsTrait;
    use ProphecyTrait;

    /** @var ProphecyInterface|ContainerInterface */
    private $container;

    /** @var DoctrineAutodiscoveryModel */
    private $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model     = $this->prophesize(DoctrineAutodiscoveryModel::class)->reveal();
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->container->get(DoctrineAutodiscoveryModel::class)->willReturn($this->model);
    }

    public function testInvokableFactoryReturnsDoctrineAutodiscoveryController(): void
    {
        $factory    = new DoctrineAutodiscoveryControllerFactory();
        $controller = $factory($this->container->reveal(), DoctrineAutodiscoveryController::class);

        $this->assertInstanceOf(DoctrineAutodiscoveryController::class, $controller);
        $this->assertAttributeSame($this->model, 'model', $controller);
    }

    public function testLegacyFactoryReturnsDoctrineAutodiscoveryController(): void
    {
        $this->container = $this->prophesize(ServiceLocatorInterface::class);
        $this->container->get(DoctrineAutodiscoveryModel::class)->willReturn($this->model);

        $factory    = new DoctrineAutodiscoveryControllerFactory();
        $controller = $factory->createService($this->container->reveal());

        $this->assertInstanceOf(DoctrineAutodiscoveryController::class, $controller);
        $this->assertAttributeSame($this->model, 'model', $controller);
    }
}
