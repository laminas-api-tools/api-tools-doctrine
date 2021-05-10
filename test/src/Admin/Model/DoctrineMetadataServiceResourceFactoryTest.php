<?php

namespace LaminasTest\ApiTools\Doctrine\Admin\Model;

use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineMetadataServiceResource;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineMetadataServiceResourceFactory;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ProphecyInterface;

class DoctrineMetadataServiceResourceFactoryTest extends TestCase
{
    /**
     * @var ProphecyInterface|ServiceManager
     */
    private $container;

    protected function setUp()
    {
        parent::setUp();

        $this->container = $this->prophesize(ServiceManager::class);
    }

    public function testFactoryReturnsDoctrineMetadataServiceResource()
    {
        $factory = new DoctrineMetadataServiceResourceFactory();

        $resource = $factory($this->container->reveal());

        $this->assertInstanceOf(DoctrineMetadataServiceResource::class, $resource);
        $this->assertSame($this->container->reveal(), $resource->getServiceManager());
    }
}
