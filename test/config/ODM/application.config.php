<?php

return array(
    'modules' => array(
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
    ),
    'module_listener_options' => array(
        'config_glob_paths' => array(
            __DIR__ . '/local.php',
        ),
        'module_paths' => array(
            'LaminasTestApiToolsGeneral' => __DIR__ . '/../../assets/module/General',
            'LaminasTestApiToolsDbMongo' => __DIR__ . '/../../assets/module/DbMongo',
            'LaminasTestApiToolsDbMongoApi' => __DIR__ . '/../../assets/module/DbMongoApi',
        ),
    ),
);
