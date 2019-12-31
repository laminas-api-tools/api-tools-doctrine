<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-doctrine for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\Doctrine\Admin;

use Laminas\ApiTools\Doctrine\Server;

return [
    'router' => [
        'routes' => [
            'api-tools-doctrine-rpc-service' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api-tools/api/module[/:name]/doctrine-rpc[/:controller_service_name]',
                    'defaults' => [
                        'controller' => Controller\DoctrineRpcService::class,
                    ],
                ],
                'may_terminate' => true,
            ],
            'api-tools-doctrine-service' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api-tools/api/module[/:name]/doctrine[/:controller_service_name]',
                    'defaults' => [
                        'controller' => Controller\DoctrineRestService::class,
                    ],
                ],
                'may_terminate' => true,
            ],
            'api-tools-doctrine-metadata-service' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api-tools/api/doctrine[/:object_manager_alias]/metadata[/:name]',
                    'defaults' => [
                        'controller' => Controller\DoctrineMetadataService::class,
                    ],
                ],
                'may_terminate' => true,
            ],
            'api-tools-doctrine-autodiscovery' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/api-tools/api/module/:name/:version/autodiscovery/doctrine/:object_manager_alias',
                    'defaults' => [
                        'controller' => Controller\DoctrineAutodiscovery::class,
                        'action' => 'discover',
                    ],
                ],
            ],
        ],
    ],

    'service_manager' => [
        // Legacy Zend Framework aliases
        'aliases' => [
            \ZF\Apigility\Doctrine\Admin\Model\DoctrineAutodiscoveryModel::class => Model\DoctrineAutodiscoveryModel::class,
            \ZF\Apigility\Doctrine\Admin\Model\DoctrineMetadataServiceResource::class => Model\DoctrineMetadataServiceResource::class,
            \ZF\Apigility\Doctrine\Admin\Model\DoctrineRestServiceModelFactory::class => Model\DoctrineRestServiceModelFactory::class,
            \ZF\Apigility\Doctrine\Admin\Model\DoctrineRestServiceResource::class => Model\DoctrineRestServiceResource::class,
            \ZF\Apigility\Doctrine\Admin\Model\DoctrineRpcServiceModelFactory::class => Model\DoctrineRpcServiceModelFactory::class,
            \ZF\Apigility\Doctrine\Admin\Model\DoctrineRpcServiceResource::class => Model\DoctrineRpcServiceResource::class,
        ],
        'factories' => [
            Model\DoctrineAutodiscoveryModel::class      => Model\DoctrineAutodiscoveryModelFactory::class,
            Model\DoctrineMetadataServiceResource::class => Model\DoctrineMetadataServiceResourceFactory::class,
            Model\DoctrineRestServiceModelFactory::class => Model\DoctrineRestServiceModelFactoryFactory::class,
            Model\DoctrineRestServiceResource::class     => Model\DoctrineRestServiceResourceFactory::class,
            Model\DoctrineRpcServiceModelFactory::class  => Model\DoctrineRpcServiceModelFactoryFactory::class,
            Model\DoctrineRpcServiceResource::class      => Model\DoctrineRpcServiceResourceFactory::class,
        ],
    ],

    'controllers' => [
        // Legacy Zend Framework aliases
        'aliases' => [
            \ZF\Apigility\Doctrine\Admin\Controller\DoctrineAutodiscovery::class => Controller\DoctrineAutodiscovery::class,
        ],
        'factories' => [
            Controller\DoctrineAutodiscovery::class => Controller\DoctrineAutodiscoveryControllerFactory::class,
        ],
    ],

    'api-tools-content-negotiation' => [
        'controllers' => [
            Controller\DoctrineAutodiscovery::class   => 'Json',
            Controller\DoctrineRestService::class     => 'HalJson',
            Controller\DoctrineRpcService::class      => 'HalJson',
            Controller\DoctrineMetadataService::class => 'HalJson',
        ],
        'accept-whitelist' => [
            Controller\DoctrineAutodiscovery::class => [
                'application/json',
                'application/*+json',
            ],
            Controller\DoctrineRpcService::class => [
                'application/json',
                'application/*+json',
            ],
            Controller\DoctrineRestService::class => [
                'application/json',
                'application/*+json',
            ],
            Controller\DoctrineMetadataService::class => [
                'application/json',
                'application/*+json',
            ],
        ],
        'content-type-whitelist' => [
            Controller\DoctrineAutodiscovery::class => [
                'application/json',
                'application/*+json',
            ],
            Controller\DoctrineRpcService::class => [
                'application/json',
                'application/*+json',
            ],
            Controller\DoctrineRestService::class => [
                'application/json',
                'application/*+json',
            ],
            Controller\DoctrineMetadataService::class => [
                'application/json',
                'application/*+json',
            ],
        ],
    ],

    'api-tools-hal' => [
        'metadata_map' => [
            Model\DoctrineRpcServiceEntity::class => [
                'hydrator'               => 'ArraySerializable',
                'route_identifier_name'  => 'controller_service_name',
                'entity_identifier_name' => 'controller_service_name',
                'route_name'             => 'api-tools-doctrine-rpc-service',
            ],
            Model\DoctrineRestServiceEntity::class => [
                'hydrator'               => 'ArraySerializable',
                'route_identifier_name'  => 'controller_service_name',
                'entity_identifier_name' => 'controller_service_name',
                'route_name'             => 'api-tools-doctrine-service',
            ],
            Model\DoctrineMetadataServiceEntity::class => [
                'hydrator'               => 'ArraySerializable',
                'entity_identifier_name' => 'name',
                'route_identifier_name'  => 'name',
                'route_name'             => 'api-tools-doctrine-metadata-service',
            ],
        ],
    ],

    'api-tools-rest' => [
        Controller\DoctrineRpcService::class => [
            'listener'                   => Model\DoctrineRpcServiceResource::class,
            'route_name'                 => 'api-tools-doctrine-rpc-service',
            'entity_class'               => Model\DoctrineRpcServiceEntity::class,
            'route_identifier_name'      => 'controller_service_name',
            'entity_http_methods'        => ['GET', 'POST', 'PATCH', 'DELETE'],
            'collection_http_methods'    => ['GET', 'POST'],
            'collection_name'            => 'doctrine-rpc',
            'collection_query_whitelist' => ['version'],
        ],
        Controller\DoctrineRestService::class => [
            'listener'                   => Model\DoctrineRestServiceResource::class,
            'route_name'                 => 'api-tools-doctrine-service',
            'entity_class'               => Model\DoctrineRestServiceEntity::class,
            'route_identifier_name'      => 'controller_service_name',
            'entity_http_methods'        => ['GET', 'POST', 'PATCH', 'DELETE'],
            'collection_http_methods'    => ['GET', 'POST'],
            'collection_name'            => 'doctrine',
            'collection_query_whitelist' => ['version'],
        ],
        Controller\DoctrineMetadataService::class => [
            'listener'                   => Model\DoctrineMetadataServiceResource::class,
            'route_name'                 => 'api-tools-doctrine-metadata-service',
            'entity_class'               => Model\DoctrineMetadataServiceEntity::class,
            'route_identifier_name'      => 'name',
            'entity_http_methods'        => ['GET'],
            'collection_http_methods'    => ['GET'],
            'collection_name'            => 'doctrine-metadata',
            'collection_query_whitelist' => ['version'],
        ],
    ],
    'api-tools-rpc' => [
        Controller\DoctrineAutodiscovery::class => [
            'http_methods' => ['GET'],
            'route_name'   => 'api-tools-doctrine-autodiscovery',
        ],
    ],
    'validator_metadata' => [
        Server\Validator\ObjectExists::class => [
            'entity_class' => 'string',
            'fields'       => 'string',
        ],
        Server\Validator\NoObjectExists::class => [
            'entity_class' => 'string',
            'fields'       => 'string',
        ],
    ],
];
