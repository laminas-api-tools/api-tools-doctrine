<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-doctrine for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\Doctrine\Server\Query\Provider\Service;

use Laminas\Mvc\Service\AbstractPluginManagerFactory;

class QueryProviderManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = QueryProviderManager::class;
}
