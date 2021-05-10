<?php

namespace Laminas\ApiTools\Doctrine\Admin\Model;

use Interop\Container\ContainerInterface;
use Laminas\ApiTools\Admin\Model\DocumentationModel;
use Laminas\ApiTools\Admin\Model\InputFilterModel;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;

class DoctrineRestServiceResourceFactory
{
    /**
     * @param ContainerInterface $container
     * @return DoctrineRestServiceResource
     */
    public function __invoke(ContainerInterface $container)
    {
        if (! $container->has(DoctrineRestServiceModelFactory::class)
            || ! $container->has(InputFilterModel::class)
            || ! $container->has(DocumentationModel::class)
        ) {
            throw new ServiceNotCreatedException(sprintf(
                '%s is missing one or more dependencies',
                DoctrineRestServiceResource::class
            ));
        }

        return new DoctrineRestServiceResource(
            $container->get(DoctrineRestServiceModelFactory::class),
            $container->get(InputFilterModel::class),
            $container->get(DocumentationModel::class)
        );
    }
}
