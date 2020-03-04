<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-doctrine for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/LICENSE.md New BSD License
 */

return [
    'doctrine' => [
        'connection' => [
            'odm_default' => [
                'server' => 'mongo',
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
