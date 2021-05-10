<?php

return [
    'modules' => [
        'DoctrineModule',
        'DoctrineORMModule',
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
        'LaminasTestApiToolsDb',
        'LaminasTestApiToolsDbApi',
    ],
    'module_listener_options' => [
        'config_glob_paths' => [
            __DIR__ . '/local.php',
        ],
        'module_paths' => [
            'LaminasTestApiToolsGeneral' => __DIR__ . '/../../assets/module/LaminasTestApiToolsGeneral',
            'LaminasTestApiToolsDb' => __DIR__ . '/../../assets/module/LaminasTestApiToolsDb',
            'LaminasTestApiToolsDbApi' => __DIR__ . '/../../assets/module/LaminasTestApiToolsDbApi',
        ],
    ],
];
