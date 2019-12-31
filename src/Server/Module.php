<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-doctrine for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\Doctrine\Server;

use Laminas\ModuleManager\Feature\AutoloaderProviderInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\DependencyIndicatorInterface;
use Laminas\ModuleManager\ModuleManager;

class Module implements ConfigProviderInterface, AutoloaderProviderInterface, DependencyIndicatorInterface
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
        return include __DIR__ . '/../../config/server.config.php';
    }

    /**
     * Module init
     *
     * @param ModuleManager $moduleManager
     */
    public function init(ModuleManager $moduleManager)
    {
        $sm = $moduleManager->getEvent()->getParam('ServiceManager');
        $serviceListener = $sm->get('ServiceListener');

        $serviceListener->addServiceManager(
            'LaminasApiToolsDoctrineQueryProviderManager',
            'api-tools-doctrine-query-provider',
            'Laminas\ApiTools\Doctrine\Server\Query\Provider\QueryProviderInterface',
            'getLaminasApiToolsDoctrineQueryProviderConfig'
        );

        $serviceListener->addServiceManager(
            'LaminasApiToolsDoctrineQueryCreateFilterManager',
            'api-tools-doctrine-query-create-filter',
            'Laminas\ApiTools\Doctrine\Server\Query\CreateFilter\QueryCreateFilterInterface',
            'getLaminasApiToolsDoctrineQueryCreateFilterConfig'
        );
    }

    /**
     * Expected to return an array of modules on which the current one depends on
     *
     * @return array
     */
    public function getModuleDependencies()
    {
        return array('Phpro\DoctrineHydrationModule');
    }
}
