<?php

declare(strict_types=1);

namespace LaminasTestApiToolsDbMongo;

use Laminas\ApiTools\Provider\ApiToolsProviderInterface;
use Laminas\Loader\StandardAutoloader;

class Module implements ApiToolsProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return [
            StandardAutoloader::class => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__,
                ],
            ],
        ];
    }
}
