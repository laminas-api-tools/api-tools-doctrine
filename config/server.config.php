<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Server;

use Laminas\ServiceManager\Factory\InvokableFactory;
use ZF\Apigility\Doctrine\Server\Query\CreateFilter\DefaultCreateFilter;
use ZF\Apigility\Doctrine\Server\Query\Provider\DefaultOdm;
use ZF\Apigility\Doctrine\Server\Query\Provider\DefaultOrm;
use ZF\Apigility\Doctrine\Server\Validator\NoObjectExists;
use ZF\Apigility\Doctrine\Server\Validator\ObjectExists;

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
            'default_odm' => Query\Provider\DefaultOdm::class,
            'default_orm' => Query\Provider\DefaultOrm::class,

            // Legacy Zend Framework aliases
            DefaultOdm::class => Query\Provider\DefaultOdm::class,
            DefaultOrm::class => Query\Provider\DefaultOrm::class,
        ],
        'factories' => [
            Query\Provider\DefaultOdm::class => InvokableFactory::class,
            Query\Provider\DefaultOrm::class => InvokableFactory::class,
        ],
    ],
    'api-tools-doctrine-query-create-filter' => [
        'aliases'   => [
            'default' => Query\CreateFilter\DefaultCreateFilter::class,

            // Legacy Zend Framework aliases
            DefaultCreateFilter::class => Query\CreateFilter\DefaultCreateFilter::class,
        ],
        'factories' => [
            Query\CreateFilter\DefaultCreateFilter::class => InvokableFactory::class,
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
            NoObjectExists::class => Validator\NoObjectExists::class,
            ObjectExists::class   => Validator\ObjectExists::class,
        ],
        'factories' => [
            Validator\NoObjectExists::class => Validator\NoObjectExistsFactory::class,
            Validator\ObjectExists::class   => Validator\ObjectExistsFactory::class,
        ],
    ],
];
