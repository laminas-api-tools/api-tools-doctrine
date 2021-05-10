<?php

return [
    'doctrine' => [
        'connection' => [
            'odm_default' => [
                'server' => 'localhost',
                'port' => '27017',
                'user' => '',
                'password' => '',
                'dbname' => 'laminas_api-tools_doctrine_server_test',
            ],
        ],
        'configuration' => [
            'odm_default' => [
                'hydrator_dir' => __DIR__ . '/../../data/DoctrineMongoODMModule/Hydrator',
            ],
        ],
    ],
];
