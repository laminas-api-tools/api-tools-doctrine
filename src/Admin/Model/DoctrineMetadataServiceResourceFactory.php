<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-doctrine for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/LICENSE.md New BSD License
 */

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
