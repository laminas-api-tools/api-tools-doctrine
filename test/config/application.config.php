<?php

declare(strict_types=1);

return [
    'modules'                 => [
        'DoctrineModule',
        'DoctrineORMModule',
        'ApiSkeletons\DoctrineORMHydrationModule',
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
        'LaminasTestApiToolsDb',
        'LaminasTestApiToolsDbApi',
    ],
    'module_listener_options' => [
        'config_glob_paths' => [
            __DIR__ . '/testing.config.php',
        ],
        'module_paths'      => [
            'LaminasTestApiToolsGeneral' => __DIR__ . '/../assets/module/LaminasTestApiToolsGeneral',
            'LaminasTestApiToolsDb'      => __DIR__ . '/../assets/module/LaminasTestApiToolsDb',
            'LaminasTestApiToolsDbApi'   => __DIR__ . '/../assets/module/LaminasTestApiToolsDbApi',
        ],
    ],
];
