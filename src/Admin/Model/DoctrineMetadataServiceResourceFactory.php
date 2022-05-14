<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Admin\Model;

use interop\container\containerinterface;

class DoctrineMetadataServiceResourceFactory
{
    /**
     * @return DoctrineMetadataServiceResource
     */
    public function __invoke(containerinterface $container)
    {
        $instance = new DoctrineMetadataServiceResource();
        $instance->setServiceManager($container);

        return $instance;
    }
}
