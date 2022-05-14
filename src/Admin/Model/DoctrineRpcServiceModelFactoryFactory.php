<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Admin\Model;

use interop\container\containerinterface;
use Laminas\ApiTools\Admin\Model\ModuleModel;
use Laminas\ApiTools\Admin\Model\ModulePathSpec;
use Laminas\ApiTools\Configuration\ConfigResourceFactory;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;

use function sprintf;

class DoctrineRpcServiceModelFactoryFactory
{
    /**
     * @return DoctrineRpcServiceModelFactory
     */
    public function __invoke(containerinterface $container)
    {
        if (
            ! $container->has(ModulePathSpec::class)
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
