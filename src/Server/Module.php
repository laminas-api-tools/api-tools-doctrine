<?php

namespace Laminas\ApiTools\Doctrine\Server;

use Laminas\ApiTools\Doctrine\Server\Query\CreateFilter\QueryCreateFilterInterface;
use Laminas\ApiTools\Doctrine\Server\Query\Provider\QueryProviderInterface;
use Laminas\ModuleManager\ModuleManager;

class Module
{
    /**
     * Returns configuration to merge with application configuration
     *
     * @return array
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
            QueryProviderInterface::class,
            'getLaminasApiToolsDoctrineQueryProviderConfig'
        );

        $serviceListener->addServiceManager(
            'LaminasApiToolsDoctrineQueryCreateFilterManager',
            'api-tools-doctrine-query-create-filter',
            QueryCreateFilterInterface::class,
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
        return ['Phpro\DoctrineHydrationModule'];
    }
}
