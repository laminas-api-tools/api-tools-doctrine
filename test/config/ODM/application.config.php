<?php

declare(strict_types=1);

return [
    'modules'                 => [
        'DoctrineModule',
        'DoctrineMongoODMModule',
        'Phpro\DoctrineHydrationModule',
        'Laminas\ApiTools',
        'Laminas\ApiTools\Admin',
        'Laminas\ApiTools\Hal',
        'Laminas\ApiTools\ContentNegotiation',
        'Laminas\ApiTools\Rest',
        'Laminas\ApiTools\Rpc',
        'Laminas\ApiTools\Configuration',
        'Laminas\ApiTools\Versioning',
        'Laminas\ApiTools\ApiProblem',
        'Laminas\ApiTools\Doctrine\Admin',
        'Laminas\ApiTools\Doctrine\Server',
        'LaminasTestApiToolsGeneral',
        'LaminasTestApiToolsDbMongo',
        'LaminasTestApiToolsDbMongoApi',
    ],
    'module_listener_options' => [
        'config_glob_paths' => [
            __DIR__ . '/local.php',
        ],
        'module_paths'      => [
            'LaminasTestApiToolsGeneral'    => __DIR__ . '/../../assets/module/LaminasTestApiToolsGeneral',
            'LaminasTestApiToolsDbMongo'    => __DIR__ . '/../../assets/module/LaminasTestApiToolsDbMongo',
            'LaminasTestApiToolsDbMongoApi' => __DIR__ . '/../../assets/module/LaminasTestApiToolsDbMongoApi',
        ],
    ],
];
