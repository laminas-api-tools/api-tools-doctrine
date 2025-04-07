<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Server;

use Laminas\ApiTools\Doctrine\Server\Query\CreateFilter\DefaultCreateFilter;
use Laminas\ApiTools\Doctrine\Server\Query\Provider\DefaultOrm;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'service_manager'                        => [
        // Legacy Zend Framework aliases
        'aliases'            => [
            'ZfApigilityDoctrineQueryProviderManager'     => 'LaminasApiToolsDoctrineQueryProviderManager',
            'ZfApigilityDoctrineQueryCreateFilterManager' => 'LaminasApiToolsDoctrineQueryCreateFilterManager',
        ],
        'abstract_factories' => [
            Resource\DoctrineResourceFactory::class,
        ],
        'factories'          => [
            'LaminasApiToolsDoctrineQueryProviderManager'
                => Query\Provider\Service\QueryProviderManagerFactory::class,
            'LaminasApiToolsDoctrineQueryCreateFilterManager'
                => Query\CreateFilter\Service\QueryCreateFilterManagerFactory::class,
        ],
    ],
    'api-tools-doctrine-query-provider'      => [
        'aliases'   => [
            'default_orm' => DefaultOrm::class,

            // Legacy Zend Framework aliases
            'ZF\Apigility\Doctrine\Server\Query\Provider\DefaultOrm' => DefaultOrm::class,
        ],
        'factories' => [
            DefaultOrm::class => InvokableFactory::class,
        ],
    ],
    'api-tools-doctrine-query-create-filter' => [
        'aliases'   => [
            'default' => DefaultCreateFilter::class,

            // Legacy Zend Framework aliases
            'ZF\Apigility\Doctrine\Server\Query\CreateFilter\DefaultCreateFilter' => DefaultCreateFilter::class,
        ],
        'factories' => [
            DefaultCreateFilter::class => InvokableFactory::class,
        ],
    ],
    'view_manager'                           => [
        'template_path_stack' => [
            'api-tools-doctrine' => __DIR__ . '/../view',
        ],
    ],
    'validators'                             => [
        // Legacy Zend Framework aliases
        'aliases'   => [
            'ZF\Apigility\Doctrine\Server\Validator\NoObjectExists' => 'Laminas\ApiTools\Doctrine\Server\Validator\NoObjectExists',
            'ZF\Apigility\Doctrine\Server\Validator\ObjectExists'   => 'Laminas\ApiTools\Doctrine\Server\Validator\ObjectExists',
        ],
        'factories' => [
            'Laminas\ApiTools\Doctrine\Server\Validator\NoObjectExists' => Validator\NoObjectExistsFactory::class,
            'Laminas\ApiTools\Doctrine\Server\Validator\ObjectExists'   => Validator\ObjectExistsFactory::class,
        ],
    ],
];
