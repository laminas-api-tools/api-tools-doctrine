<?php

namespace Laminas\ApiTools\Doctrine\Server\Query\Provider\Service;

use Laminas\Mvc\Service\AbstractPluginManagerFactory;

class QueryProviderManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = 'Laminas\ApiTools\Doctrine\Server\Query\Provider\Service\QueryProviderManager';
}
