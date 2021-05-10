<?php

namespace Laminas\ApiTools\Doctrine\Admin\Model;

use Interop\Container\ContainerInterface;

class DoctrineMetadataServiceResourceFactory
{
    /**
     * @param ContainerInterface $container
     * @return DoctrineMetadataServiceResource
     */
    public function __invoke(ContainerInterface $container)
    {
        $instance = new DoctrineMetadataServiceResource();
        $instance->setServiceManager($container);

        return $instance;
    }
}
