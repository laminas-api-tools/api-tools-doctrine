<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Admin\Model;

use Interop\Container\ContainerInterface;

class DoctrineMetadataServiceResourceFactory
{
    /**
     * @return DoctrineMetadataServiceResource
     */
    public function __invoke(ContainerInterface $container)
    {
        $instance = new DoctrineMetadataServiceResource();
        $instance->setServiceManager($container);

        return $instance;
    }
}
