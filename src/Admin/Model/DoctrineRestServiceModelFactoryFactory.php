<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Admin\Model;

use Interop\Container\ContainerInterface;
use Laminas\ApiTools\Admin\Model\ModuleModel;
use Laminas\ApiTools\Admin\Model\ModulePathSpec;
use Laminas\ApiTools\Configuration\ConfigResourceFactory;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;

use function sprintf;

class DoctrineRestServiceModelFactoryFactory
{
    /**
     * @return DoctrineRestServiceModelFactory
     */
    public function __invoke(ContainerInterface $container)
    {
        if (
            ! $container->has(ModulePathSpec::class)
            || ! $container->has(ModuleModel::class)
            || ! $container->has(ConfigResourceFactory::class)
            || ! $container->has('SharedEventManager')
        ) {
            throw new ServiceNotCreatedException(sprintf(
                '%s is missing one or more dependencies from Laminas\ApiTools\Configuration',
                DoctrineRestServiceModelFactory::class
            ));
        }

        $sharedEvents = $container->get('SharedEventManager');
        $this->attachSharedListeners($sharedEvents);

        $instance = new DoctrineRestServiceModelFactory(
            $container->get(ModulePathSpec::class),
            $container->get(ConfigResourceFactory::class),
            $sharedEvents,
            $container->get(ModuleModel::class)
        );
        $instance->setServiceManager($container);

        return $instance;
    }

    /**
     * Attach shared listeners to the DoctrineRestServiceModel.
     *
     * @return void
     */
    private function attachSharedListeners(SharedEventManagerInterface $sharedEvents)
    {
        $sharedEvents->attach(
            DoctrineRestServiceModel::class,
            'fetch',
            [DoctrineRestServiceModel::class, 'onFetch']
        );
    }
}
