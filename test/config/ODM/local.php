<?php

declare(strict_types=1);

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
