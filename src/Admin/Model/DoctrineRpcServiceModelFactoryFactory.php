<?php

namespace Laminas\ApiTools\Doctrine\Admin\Model;

use Interop\Container\ContainerInterface;
use Laminas\ApiTools\Admin\Model\ModuleModel;
use Laminas\ApiTools\Admin\Model\ModulePathSpec;
use Laminas\ApiTools\Configuration\ConfigResourceFactory;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;

class DoctrineRpcServiceModelFactoryFactory
{
    /**
     * @param ContainerInterface $container
     * @return DoctrineRpcServiceModelFactory
     */
    public function __invoke(ContainerInterface $container)
    {
        if (! $container->has(ModulePathSpec::class)
            || ! $container->has(ModuleModel::class)
            || ! $container->has(ConfigResourceFactory::class)
            || ! $container->has('SharedEventManager')
        ) {
            throw new ServiceNotCreatedException(sprintf(
                '%s is missing one or more dependencies from Laminas\ApiTools\Configuration',
                DoctrineRpcServiceModelFactory::class
            ));
        }

        $moduleModel    = $container->get(ModuleModel::class);
        $configFactory  = $container->get(ConfigResourceFactory::class);
        $modulePathSpec = $container->get(ModulePathSpec::class);
        $sharedEvents   = $container->get('SharedEventManager');

        return new DoctrineRpcServiceModelFactory($modulePathSpec, $configFactory, $sharedEvents, $moduleModel);
    }
}
