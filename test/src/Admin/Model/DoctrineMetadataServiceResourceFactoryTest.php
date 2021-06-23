<?php

declare(strict_types=1);

namespace LaminasTest\ApiTools\Doctrine\Admin\Model;

use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineMetadataServiceResource;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineMetadataServiceResourceFactory;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ProphecyInterface;

class DoctrineMetadataServiceResourceFactoryTest extends TestCase
{
    use ProphecyTrait;

    /** @var ProphecyInterface|ServiceManager */
    private $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = $this->prophesize(ServiceManager::class);
    }

    public function testFactoryReturnsDoctrineMetadataServiceResource(): void
    {
        $factory = new DoctrineMetadataServiceResourceFactory();

        $resource = $factory($this->container->reveal());

        $this->assertInstanceOf(DoctrineMetadataServiceResource::class, $resource);
        $this->assertSame($this->container->reveal(), $resource->getServiceManager());
    }
}
