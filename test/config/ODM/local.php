<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-doctrine for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/LICENSE.md New BSD License
 */

return [
    'doctrine' => [
        'connection'    => [
            'odm_default' => [
                'connectionString' => getenv('TESTS_LAMINAS_API_TOOLS_DOCTRINE_EXTMONGODB_CONNECTSTRING'),
                'dbname'           => getenv('TESTS_LAMINAS_API_TOOLS_DOCTRINE_EXTMONGODB_DATABASE'),
            ],
        ],
        'configuration' => [
            'odm_default' => [
                'hydrator_dir' => __DIR__ . '/../../data/DoctrineMongoODMModule/Hydrator',
            ],
        ],
    ],
];
