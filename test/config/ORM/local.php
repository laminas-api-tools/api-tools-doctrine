<?php

return array(
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'configuration' => 'orm_default',
                'eventmanager'  => 'orm_default',
                'driverClass'   => 'Doctrine\DBAL\Driver\PDOSqlite\Driver',
                'params' => array(
                    'memory' => true,
                ),
            ),
        ),
    ),
    'api-tools-orm-collection-filter' => array(
        'invokables' => array(
            'innerjoin' => 'Laminas\ApiTools\Doctrine\Server\Collection\Filter\ORM\InnerJoin',
        ),
    ),
);
