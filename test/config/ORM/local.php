<?php

use LaminasTestApiToolsDb\Type\RevType;

return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'configuration' => 'orm_default',
                'eventmanager'  => 'orm_default',
                'driverClass'   => \Doctrine\DBAL\Driver\PDOSqlite\Driver::class,
                'params' => [
                    'memory' => true,
                ],
            ],
        ],
        'configuration' => [
            'orm_default' => [
                'types' => [
                    RevType::NAME => RevType::class,
                ],
            ],
        ],
    ],
];
