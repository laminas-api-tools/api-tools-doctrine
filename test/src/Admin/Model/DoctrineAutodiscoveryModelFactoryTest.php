<?php

declare(strict_types=1);

namespace LaminasTest\ApiTools\Doctrine\Admin\Model;

use Interop\Container\ContainerInterface;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineAutodiscoveryModel;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineAutodiscoveryModelFactory;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LaminasTest\ApiTools\Doctrine\DeprecatedAssertionsTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ProphecyInterface;

class DoctrineAutodiscoveryModelFactoryTest extends TestCase
{
    use DeprecatedAssertionsTrait;
    use ProphecyTrait;

    /** @var ProphecyInterface|ServiceLocatorInterface|ContainerInterface */
    private $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = $this->prophesize(ServiceLocatorInterface::class);
        $this->container->willImplement(ContainerInterface::class);
    }

    public function testFactoryRaisesExceptionIfConfigServiceIsMissing(): void
    {
        $factory = new DoctrineAutodiscoveryModelFactory();

        $this->container->has('config')->willReturn(false);

        $this->expectException(ServiceNotCreatedException::class);
        $this->expectExceptionMessage('config service is not present');
        $factory($this->container->reveal());
    }

    public function testFactoryReturnsDoctrineAutodiscoveryModelComposingConfigAndContainer(): void
    {
        $factory = new DoctrineAutodiscoveryModelFactory();

        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn([]);

        $model = $factory($this->container->reveal());

        $this->assertInstanceOf(DoctrineAutodiscoveryModel::class, $model);
        $this->assertAttributeEquals([], 'config', $model);
        $this->assertSame($this->container->reveal(), $model->getServiceLocator());
    }
}
