<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Server\Query\CreateFilter\Service;

use Laminas\Mvc\Service\AbstractPluginManagerFactory;

class QueryCreateFilterManagerFactory extends AbstractPluginManagerFactory
{
    public const PLUGIN_MANAGER_CLASS = QueryCreateFilterManager::class;
}
