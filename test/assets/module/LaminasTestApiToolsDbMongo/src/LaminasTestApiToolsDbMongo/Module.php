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

    /**
     * @return string[][][]
     * @psalm-return array<string, array<string, array<string, string>>>
     */
    public function getAutoloaderConfig(): array
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
