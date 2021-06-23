<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Admin\Model;

use Interop\Container\ContainerInterface;
use Laminas\ApiTools\Admin\Model\DocumentationModel;
use Laminas\ApiTools\Admin\Model\InputFilterModel;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;

use function sprintf;

class DoctrineRpcServiceResourceFactory
{
    /**
     * @return DoctrineRpcServiceResource
     * @throws ServiceNotCreatedException When missing required dependencies.
     */
    public function __invoke(ContainerInterface $container)
    {
        if (
            ! $container->has(DoctrineRpcServiceModelFactory::class)
            || ! $container->has(InputFilterModel::class)
            || ! $container->has('ControllerManager')
            || ! $container->has(DocumentationModel::class)
        ) {
            throw new ServiceNotCreatedException(sprintf(
                '%s is missing one or more dependencies from Laminas\ApiTools\Configuration',
                DoctrineRpcServiceResource::class
            ));
        }

        return new DoctrineRpcServiceResource(
            $container->get(DoctrineRpcServiceModelFactory::class),
            $container->get(InputFilterModel::class),
            $container->get('ControllerManager'),
            $container->get(DocumentationModel::class)
        );
    }
}
