<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Server\Query\Provider\Service;

use Laminas\Mvc\Service\AbstractPluginManagerFactory;

class QueryProviderManagerFactory extends AbstractPluginManagerFactory
{
    public const PLUGIN_MANAGER_CLASS = QueryProviderManager::class;
}
