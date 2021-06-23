<?php

declare(strict_types=1);

use Doctrine\DBAL\Driver\PDOSqlite\Driver;
use LaminasTestApiToolsDb\Type\RevType;

return [
    'doctrine' => [
        'connection'    => [
            'orm_default' => [
                'configuration' => 'orm_default',
                'eventmanager'  => 'orm_default',
                'driverClass'   => Driver::class,
                'params'        => [
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
