<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-doctrine for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\Doctrine\Server;

use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'service_manager' => [
        // Legacy Zend Framework aliases
        'aliases' => [
            'ZfApigilityDoctrineQueryProviderManager' => 'LaminasApiToolsDoctrineQueryProviderManager',
            'ZfApigilityDoctrineQueryCreateFilterManager' => 'LaminasApiToolsDoctrineQueryCreateFilterManager',
        ],
        'abstract_factories' => [
            Resource\DoctrineResourceFactory::class,
        ],
        'factories' => [
            'LaminasApiToolsDoctrineQueryProviderManager'
                => Query\Provider\Service\QueryProviderManagerFactory::class,
            'LaminasApiToolsDoctrineQueryCreateFilterManager'
                => Query\CreateFilter\Service\QueryCreateFilterManagerFactory::class,
        ],
    ],

    'api-tools-doctrine-query-provider' => [
        'aliases' => [
            'default_odm' => Query\Provider\DefaultOdm::class,
            'default_orm' => Query\Provider\DefaultOrm::class,

            // Legacy Zend Framework aliases
            \ZF\Apigility\Doctrine\Server\Query\Provider\DefaultOdm::class => Query\Provider\DefaultOdm::class,
            \ZF\Apigility\Doctrine\Server\Query\Provider\DefaultOrm::class => Query\Provider\DefaultOrm::class,
        ],
        'factories' => [
            Query\Provider\DefaultOdm::class => InvokableFactory::class,
            Query\Provider\DefaultOrm::class => InvokableFactory::class,
        ],
    ],

    'api-tools-doctrine-query-create-filter' => [
        'aliases' => [
            'default' => Query\CreateFilter\DefaultCreateFilter::class,

            // Legacy Zend Framework aliases
            \ZF\Apigility\Doctrine\Server\Query\CreateFilter\DefaultCreateFilter::class
                => Query\CreateFilter\DefaultCreateFilter::class,
        ],
        'factories' => [
            Query\CreateFilter\DefaultCreateFilter::class => InvokableFactory::class,
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            'api-tools-doctrine' => __DIR__ . '/../view',
        ],
    ],

    'validators' => [
        // Legacy Zend Framework aliases
        'aliases' => [
            \ZF\Apigility\Doctrine\Server\Validator\NoObjectExists::class => Validator\NoObjectExists::class,
            \ZF\Apigility\Doctrine\Server\Validator\ObjectExists::class => Validator\ObjectExists::class,
        ],
        'factories' => [
            Validator\NoObjectExists::class => Validator\NoObjectExistsFactory::class,
            Validator\ObjectExists::class   => Validator\ObjectExistsFactory::class,
        ],
    ],
];
