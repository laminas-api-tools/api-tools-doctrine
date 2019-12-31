<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-doctrine for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\Doctrine\Admin;

use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineMetadataServiceResource;
use Laminas\ModuleManager\Feature\AutoloaderProviderInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\DependencyIndicatorInterface;
use Laminas\ModuleManager\Feature\ServiceProviderInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;

class Module implements
    ConfigProviderInterface,
    AutoloaderProviderInterface,
    ServiceProviderInterface,
    DependencyIndicatorInterface
{
    /**
     * Return an array for passing to Laminas\Loader\AutoloaderFactory.
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Laminas\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }

    /**
     * Returns configuration to merge with application configuration
     *
     * @return array|\Traversable
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/admin.config.php';
    }

    /**
     * Expected to return \Laminas\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|\Laminas\ServiceManager\Config
     */
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                // This resource pulls the object manager dynamically
                // so it needs access to the service manager
                'Laminas\ApiTools\Doctrine\Admin\Model\DoctrineMetadataServiceResource' => function ($services) {
                    $instance = new DoctrineMetadataServiceResource();
                    $instance->setServiceManager($services);

                    return $instance;
                },
                'Laminas\ApiTools\Doctrine\Admin\Model\DoctrineAutodiscoveryModel' => function ($services) {
                    if (!$services->has('Config')) {
                        // @codeCoverageIgnoreStart
                        throw new ServiceNotCreatedException(
                            'Cannot create Laminas\ApiTools\Doctrine\Admin\Model\DoctrineAutodiscoveryModel
                            service because Config service is not present'
                        );
                        // @codeCoverageIgnoreEnd
                    }
                    $config = $services->get('Config');

                    return new Model\DoctrineAutodiscoveryModel($config);
                },
                'Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceModelFactory' => function ($services) {
                    if (!$services->has('Laminas\ApiTools\Admin\Model\ModulePathSpec')
                        || !$services->has('Laminas\ApiTools\Configuration\ConfigResourceFactory')
                        || !$services->has('Laminas\ApiTools\Admin\Model\ModuleModel')
                        || !$services->has('SharedEventManager')
                    ) {
                        // @codeCoverageIgnoreStart
                        throw new ServiceNotCreatedException(
                            'Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceModelFactory is missing one'
                            . ' or more dependencies from Laminas\ApiTools\Configuration'
                        );
                        // @codeCoverageIgnoreEnd
                    }
                    $moduleModel   = $services->get('Laminas\ApiTools\Admin\Model\ModuleModel');
                    $modulePathSpec = $services->get('Laminas\ApiTools\Admin\Model\ModulePathSpec');
                    $configFactory = $services->get('Laminas\ApiTools\Configuration\ConfigResourceFactory');
                    $sharedEvents  = $services->get('SharedEventManager');

                    // Wire Doctrine-Connected fetch listener
                    $sharedEvents->attach(
                        __NAMESPACE__ . '\Model\DoctrineRestServiceModel',
                        'fetch',
                        __NAMESPACE__ . '\Model\DoctrineRestServiceModel::onFetch'
                    );

                    $instance = new Model\DoctrineRestServiceModelFactory(
                        $modulePathSpec,
                        $configFactory,
                        $sharedEvents,
                        $moduleModel
                    );
                    $instance->setServiceManager($services);

                    return $instance;
                },
                'Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceResource' => function ($services) {
                    if (!$services->has('Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceModelFactory')) {
                        // @codeCoverageIgnoreStart
                        throw new ServiceNotCreatedException(
                            'Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceResource is missing one or more'
                            . ' dependencies'
                        );
                        // @codeCoverageIgnoreEnd
                    }
                    if (!$services->has('Laminas\ApiTools\Admin\Model\InputFilterModel')) {
                        // @codeCoverageIgnoreStart
                        throw new ServiceNotCreatedException(
                            'Laminas\ApiTools\Admin\Model\RestServiceResource is missing one or more dependencies'
                        );
                        // @codeCoverageIgnoreEnd
                    }
                    $factory = $services->get('Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceModelFactory');
                    $inputFilterModel = $services->get('Laminas\ApiTools\Admin\Model\InputFilterModel');
                    $documentationModel = $services->get('Laminas\ApiTools\Admin\Model\DocumentationModel');

                    return new Model\DoctrineRestServiceResource($factory, $inputFilterModel, $documentationModel);
                },

                'Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRpcServiceModelFactory' => function ($services) {
                    if (!$services->has('Laminas\ApiTools\Admin\Model\ModulePathSpec')
                        || !$services->has('Laminas\ApiTools\Configuration\ConfigResourceFactory')
                        || !$services->has('Laminas\ApiTools\Admin\Model\ModuleModel')
                        || !$services->has('SharedEventManager')
                    ) {
                        // @codeCoverageIgnoreStart
                        throw new ServiceNotCreatedException(
                            'Laminas\ApiTools\Admin\Model\RpcServiceModelFactory is missing one or more dependencies'
                            . ' from Laminas\ApiTools\Configuration'
                        );
                        // @codeCoverageIgnoreEnd
                    }
                    $moduleModel   = $services->get('Laminas\ApiTools\Admin\Model\ModuleModel');
                    $configFactory = $services->get('Laminas\ApiTools\Configuration\ConfigResourceFactory');
                    $modulePathSpec = $services->get('Laminas\ApiTools\Admin\Model\ModulePathSpec');
                    $sharedEvents  = $services->get('SharedEventManager');

                    return new Model\DoctrineRpcServiceModelFactory(
                        $modulePathSpec,
                        $configFactory,
                        $sharedEvents,
                        $moduleModel
                    );
                },

                'Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRpcServiceResource' => function ($services) {
                    // @codeCoverageIgnoreStart
                    if (!$services->has('Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRpcServiceModelFactory')) {
                        throw new ServiceNotCreatedException(
                            'Laminas\ApiTools\Admin\Model\RpcServiceResource is missing RpcServiceModelFactory dependency'
                        );
                    }
                    if (!$services->has('Laminas\ApiTools\Admin\Model\InputFilterModel')) {
                        throw new ServiceNotCreatedException(
                            'Laminas\ApiTools\Admin\Model\RpcServiceResource is missing InputFilterModel dependency'
                        );
                    }
                    if (!$services->has('ControllerManager')) {
                        throw new ServiceNotCreatedException(
                            'Laminas\ApiTools\Admin\Model\RpcServiceResource is missing ControllerManager dependency'
                        );
                    }
                    // @codeCoverageIgnoreEnd

                    $factory = $services->get('Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRpcServiceModelFactory');
                    $inputFilterModel = $services->get('Laminas\ApiTools\Admin\Model\InputFilterModel');
                    $controllerManager = $services->get('ControllerManager');

                    return new Model\DoctrineRpcServiceResource($factory, $inputFilterModel, $controllerManager);
                },
            )
        );
    }

    /**
     * Expected to return an array of modules on which the current one depends on
     *
     * @return array
     */
    public function getModuleDependencies()
    {
        return array('Laminas\ApiTools\Admin');
    }
}
