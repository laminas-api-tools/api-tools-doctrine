<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-doctrine for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\Doctrine\Admin;

use Laminas\ApiTools\Admin\Model\RestServiceResource;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceResource;
use Laminas\ApiTools\Hal\Resource;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;

class Module
{
    /**
     * @var \Closure
     */
    protected $urlHelper;

    /**
     * @var \Laminas\ServiceManager\ServiceLocatorInterface
     */
    protected $sm;

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

    public function getConfig()
    {
        return include __DIR__ . '/../../config/admin.config.php';
    }

    public function getServiceConfig()
    {
        return array('factories' => array(
            'Laminas\ApiTools\Doctrine\Admin\Model\DoctrineMetadataServiceResource' => function ($services) {

                $resource = new Model\DoctrineMetadataServiceResource();

                return $resource;
            },

            'Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceModelFactory' => function ($services) {
                if (!$services->has('Laminas\ApiTools\Configuration\ModuleUtils')
                    || !$services->has('Laminas\ApiTools\Configuration\ConfigResourceFactory')
                    || !$services->has('Laminas\ApiTools\Admin\Model\ModuleModel')
                    || !$services->has('SharedEventManager')
                ) {
                    // @codeCoverageIgnoreStart
                    throw new ServiceNotCreatedException(
                        'Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceModelFactory is missing one or more dependencies from Laminas\ApiTools\Configuration'
                    );
                    // @codeCoverageIgnoreEnd
                }
                $moduleModel   = $services->get('Laminas\ApiTools\Admin\Model\ModuleModel');
                $moduleUtils   = $services->get('Laminas\ApiTools\Configuration\ModuleUtils');
                $configFactory = $services->get('Laminas\ApiTools\Configuration\ConfigResourceFactory');
                $sharedEvents  = $services->get('SharedEventManager');

                // Wire Doctrine-Connected fetch listener
                $sharedEvents->attach(__NAMESPACE__ . '\Admin\Model\DoctrineRestServiceModel', 'fetch', 'Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceModel::onFetch');

                return new Model\DoctrineRestServiceModelFactory($moduleUtils, $configFactory, $sharedEvents, $moduleModel);
            },
            'Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceResource' => function ($services) {
                if (!$services->has('Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceModelFactory')) {
                    // @codeCoverageIgnoreStart
                    throw new ServiceNotCreatedException(
                        'Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceResource is missing one or more dependencies'
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

                return new DoctrineRestServiceResource($factory, $inputFilterModel, $documentationModel);
            },

            'Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRpcServiceModelFactory' => function ($services) {
                if (!$services->has('Laminas\ApiTools\Configuration\ModuleUtils')
                    || !$services->has('Laminas\ApiTools\Configuration\ConfigResourceFactory')
                    || !$services->has('Laminas\ApiTools\Admin\Model\ModuleModel')
                    || !$services->has('SharedEventManager')
                ) {
                    // @codeCoverageIgnoreStart
                    throw new ServiceNotCreatedException(
                        'Laminas\ApiTools\Admin\Model\RpcServiceModelFactory is missing one or more dependencies from Laminas\ApiTools\Configuration'
                    );
                    // @codeCoverageIgnoreEnd
                }
                $moduleModel   = $services->get('Laminas\ApiTools\Admin\Model\ModuleModel');
                $moduleUtils   = $services->get('Laminas\ApiTools\Configuration\ModuleUtils');
                $configFactory = $services->get('Laminas\ApiTools\Configuration\ConfigResourceFactory');
                $sharedEvents  = $services->get('SharedEventManager');

                return new Model\DoctrineRpcServiceModelFactory($moduleUtils, $configFactory, $sharedEvents, $moduleModel);
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
        ));
    }
}
