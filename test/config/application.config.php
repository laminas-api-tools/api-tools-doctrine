<?php

return array(
    'modules' => array(
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
    ),
    'module_listener_options' => array(
        'config_glob_paths' => array(
            __DIR__ . '/testing.config.php',
        ),
        'module_paths' => array(
            'LaminasTestApiToolsGeneral' => __DIR__ . '/../../assets/module/General',
            'LaminasTestApiToolsDb' => __DIR__ . '/../assets/module/Db',
            'LaminasTestApiToolsDbApi' => __DIR__ . '/../assets/module/DbApi',
        ),
    ),
);
