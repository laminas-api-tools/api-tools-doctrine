<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Server\Resource;

use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectManager;
use Interop\Container\ContainerInterface;
use Laminas\ApiTools\Doctrine\Server\Query\CreateFilter\QueryCreateFilterInterface;
use Laminas\ApiTools\Hal\Plugin\Hal;
use Laminas\Hydrator\HydratorInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use RuntimeException;

use function class_exists;
use function is_array;
use function is_subclass_of;
use function ltrim;
use function sprintf;

class DoctrineResourceFactory implements AbstractFactoryInterface
{
    /**
     * Can this factory create the requested service?
     *
     * @param string $requestedName
     * @return bool
     * @throws ServiceNotFoundException
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        if (! $container->has('config')) {
            return false;
        }

        $config = $container->get('config');

        if (
            ! isset($config['api-tools']['doctrine-connected'])
            || ! is_array($config['api-tools']['doctrine-connected'])
        ) {
            return false;
        }

        $config = $config['api-tools']['doctrine-connected']; //[$requestedName];

        if (
            ! isset($config[$requestedName])
            || ! is_array($config[$requestedName])
            || ! $this->isValidConfig($config[$requestedName], $requestedName, $container)
        ) {
            return false;
        }

        return true;
    }

    /**
     * Can this factory create the requested service? (v2)
     *
     * Provided for backwards compatiblity; proxies to canCreate().
     *
     * @param string $name
     * @param string $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $container, $name, $requestedName)
    {
        return $this->canCreate($container, $requestedName);
    }

    /**
     * Create and return the doctrine-connected resource.
     *
     * @param string $requestedName
     * @param null|array $options
     * @return DoctrineResource
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $config                  = $container->get('config');
        $doctrineConnectedConfig = $config['api-tools']['doctrine-connected'][$requestedName];
        $doctrineHydratorConfig  = $config['doctrine-hydrator'];

        $restConfig = null;
        foreach ($config['api-tools-rest'] as $restControllerConfig) {
            if ($restControllerConfig['listener'] === $requestedName) {
                $restConfig = $restControllerConfig;
                break;
            }
        }

        if ($restConfig === null) {
            throw new RuntimeException(
                sprintf('No api-tools-rest configuration found for resource %s', $requestedName)
            );
        }

        $resourceClass = $this->getResourceClassFromConfig($doctrineConnectedConfig, $requestedName);
        $objectManager = $container->get($doctrineConnectedConfig['object_manager']);
        $entityFactory = ! empty($doctrineConnectedConfig['entity_factory'])
            ? $container->get($doctrineConnectedConfig['entity_factory'])
            : null;

        $hydrator            = $this->loadHydrator($container, $doctrineConnectedConfig, $doctrineHydratorConfig);
        $queryProviders      = $this->loadQueryProviders($container, $doctrineConnectedConfig, $objectManager);
        $queryCreateFilter   = $this->loadQueryCreateFilter($container, $doctrineConnectedConfig, $objectManager);
        $configuredListeners = $this->loadConfiguredListeners($container, $doctrineConnectedConfig);

        /** @var DoctrineResource $listener */
        $listener = new $resourceClass($entityFactory);
        $listener->setSharedEventManager($container->get('Application')->getEventManager()->getSharedManager());
        $listener->setObjectManager($objectManager);
        $listener->setHydrator($hydrator);
        $listener->setQueryProviders($queryProviders);
        $listener->setQueryCreateFilter($queryCreateFilter);
        $listener->setEntityIdentifierName($restConfig['entity_identifier_name']);
        $listener->setRouteIdentifierName($restConfig['route_identifier_name']);

        if ($configuredListeners) {
            $events = $listener->getEventManager();
            foreach ($configuredListeners as $configuredListener) {
                $configuredListener->attach($events);
            }
        }

        return $listener;
    }

    /**
     * Retrieve the resource class based on the provided configuration.
     *
     * Defaults to Laminas\ApiTools\Doctrine\Server\Resource\DoctrineResource.
     *
     * @param array $config
     * @param string $requestedName
     * @return string
     * @throws ServiceNotCreatedException If the discovered resource class
     *     does not exist or is not a subclass of DoctrineResource.
     */
    protected function getResourceClassFromConfig($config, $requestedName)
    {
        $defaultClass = DoctrineResource::class;

        $resourceClass = $config['class'] ?? $requestedName;
        $resourceClass = $this->normalizeClassname($resourceClass);

        if (! class_exists($resourceClass) || ! is_subclass_of($resourceClass, $defaultClass)) {
            throw new ServiceNotCreatedException(sprintf(
                'Unable to create instance for service "%s"; resource class "%s" cannot be found or does not extend %s',
                $requestedName,
                $resourceClass,
                $defaultClass
            ));
        }

        return $resourceClass;
    }

    /**
     * Tests if the configuration is valid
     *
     * If the configuration has a "object_manager" key, and that service exists,
     * then the configuration is valid.
     *
     * @param array $config
     * @param string $requestedName
     * @return bool
     */
    protected function isValidConfig(array $config, $requestedName, ContainerInterface $container)
    {
        if (
            ! isset($config['object_manager'])
            || ! $container->has($config['object_manager'])
        ) {
            return false;
        }

        return true;
    }

    /**
     * Create and return the doctrine-connected resource (v2).
     *
     * Provided for backwards compatibility; proxies to __invoke().
     *
     * @param string $name
     * @param string $requestedName
     * @return DoctrineResource
     */
    public function createServiceWithName(ServiceLocatorInterface $container, $name, $requestedName)
    {
        return $this($container, $requestedName);
    }

    /**
     * @param string $className
     * @return string
     */
    protected function normalizeClassname($className)
    {
        return '\\' . ltrim($className, '\\');
    }

    /**
     * @param array $doctrineConnectedConfig
     * @param array $doctrineHydratorConfig
     * @return HydratorInterface
     */
    protected function loadHydrator(
        ContainerInterface $container,
        array $doctrineConnectedConfig,
        array $doctrineHydratorConfig
    ) {
        if (! isset($doctrineConnectedConfig['hydrator'])) {
            return null;
        }

        if (! $container->has('HydratorManager')) {
            return null;
        }

        $hydratorManager = $container->get('HydratorManager');
        if (! $hydratorManager->has($doctrineConnectedConfig['hydrator'])) {
            return null;
        }

        // Set the hydrator for the entity for this resource to the hydrator
        // configured for the resource.  This removes per-entity hydrator configuration
        // allowing multiple hydrators per resource.
        if (isset($doctrineConnectedConfig['hydrator'])) {
            $entityClass = $doctrineHydratorConfig[$doctrineConnectedConfig['hydrator']]['entity_class'];
            $viewHelpers = $container->get('ViewHelperManager');
            /** @var Hal $hal */
            $hal = $viewHelpers->get('Hal');
            $hal->getEntityHydratorManager()->addHydrator($entityClass, $doctrineConnectedConfig['hydrator']);
        }

        return $hydratorManager->get($doctrineConnectedConfig['hydrator']);
    }

    /**
     * @param array $config
     * @param ObjectManager $objectManager
     * @return QueryCreateFilterInterface
     */
    protected function loadQueryCreateFilter(ContainerInterface $container, array $config, $objectManager)
    {
        $createFilterManager = $container->get('LaminasApiToolsDoctrineQueryCreateFilterManager');
        $filterManagerAlias  = $config['query_create_filter'] ?? 'default';

        /** @var QueryCreateFilterInterface $queryCreateFilter */
        $queryCreateFilter = $createFilterManager->get($filterManagerAlias);

        // Set object manager for all query providers
        $queryCreateFilter->setObjectManager($objectManager);

        return $queryCreateFilter;
    }

    /**
     * @param array $config
     * @param ObjectManager $objectManager
     * @return array
     * @throws ServiceNotCreatedException
     */
    protected function loadQueryProviders(ContainerInterface $serviceLocator, array $config, $objectManager)
    {
        $queryProviders = [];
        $queryManager   = $serviceLocator->get('LaminasApiToolsDoctrineQueryProviderManager');

        // Load default query provider
        if (
            class_exists(EntityManager::class)
            && $objectManager instanceof EntityManager
        ) {
            $queryProviders['default'] = $queryManager->get('default_orm');
        } else {
            throw new ServiceNotCreatedException('No valid doctrine module is found for objectManager.');
        }

        // Load custom query providers
        if (isset($config['query_providers'])) {
            foreach ($config['query_providers'] as $method => $plugin) {
                $queryProviders[$method] = $queryManager->get($plugin);
            }
        }

        // Set object manager for all query providers
        foreach ($queryProviders as $provider) {
            $provider->setObjectManager($objectManager);
        }

        return $queryProviders;
    }

    /**
     * @param array $config
     * @return array
     */
    protected function loadConfiguredListeners(ContainerInterface $container, array $config)
    {
        if (! isset($config['listeners'])) {
            return [];
        }

        $listeners = [];
        foreach ($config['listeners'] as $listener) {
            $listeners[] = $container->get($listener);
        }

        return $listeners;
    }
}
