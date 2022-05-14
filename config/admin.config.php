<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Admin;

use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineAutodiscoveryModel;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineMetadataServiceResource;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceModelFactory;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceResource;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRpcServiceModelFactory;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRpcServiceResource;

return [
    'router'                        => [
        'routes' => [
            'api-tools-doctrine-rpc-service'      => [
                'type'          => 'segment',
                'options'       => [
                    'route'    => '/api-tools/api/module[/:name]/doctrine-rpc[/:controller_service_name]',
                    'defaults' => [
                        'controller' => 'ZF\Apigility\Doctrine\Admin\Controller\DoctrineRpcService',
                    ],
                ],
                'may_terminate' => true,
            ],
            'api-tools-doctrine-service'          => [
                'type'          => 'segment',
                'options'       => [
                    'route'    => '/api-tools/api/module[/:name]/doctrine[/:controller_service_name]',
                    'defaults' => [
                        'controller' => Controller\DoctrineRestService::class,
                    ],
                ],
                'may_terminate' => true,
            ],
            'api-tools-doctrine-metadata-service' => [
                'type'          => 'segment',
                'options'       => [
                    'route'    => '/api-tools/api/doctrine[/:object_manager_alias]/metadata[/:name]',
                    'defaults' => [
                        'controller' => 'ZF\Apigility\Doctrine\Admin\Controller\DoctrineMetadataService',
                    ],
                ],
                'may_terminate' => true,
            ],
            'api-tools-doctrine-autodiscovery'    => [
                'type'    => 'segment',
                'options' => [
                    'route'    => '/api-tools/api/module/:name/:version/autodiscovery/doctrine/:object_manager_alias',
                    'defaults' => [
                        'controller' => 'ZF\Apigility\Doctrine\Admin\Controller\DoctrineAutodiscovery',
                        'action'     => 'discover',
                    ],
                ],
            ],
        ],
    ],
    'service_manager'               => [
        // Legacy Zend Framework aliases
        'aliases'   => [
            DoctrineAutodiscoveryModel::class      => DoctrineAutodiscoveryModel::class,
            DoctrineMetadataServiceResource::class => DoctrineMetadataServiceResource::class,
            DoctrineRestServiceModelFactory::class => DoctrineRestServiceModelFactory::class,
            DoctrineRestServiceResource::class     => DoctrineRestServiceResource::class,
            DoctrineRpcServiceModelFactory::class  => DoctrineRpcServiceModelFactory::class,
            DoctrineRpcServiceResource::class      => DoctrineRpcServiceResource::class,
        ],
        'factories' => [
            DoctrineAutodiscoveryModel::class      => Model\DoctrineAutodiscoveryModelFactory::class,
            DoctrineMetadataServiceResource::class => Model\DoctrineMetadataServiceResourceFactory::class,
            DoctrineRestServiceModelFactory::class => Model\DoctrineRestServiceModelFactoryFactory::class,
            DoctrineRestServiceResource::class     => Model\DoctrineRestServiceResourceFactory::class,
            DoctrineRpcServiceModelFactory::class  => Model\DoctrineRpcServiceModelFactoryFactory::class,
            DoctrineRpcServiceResource::class      => Model\DoctrineRpcServiceResourceFactory::class,
        ],
    ],
    'controllers'                   => [
        // Legacy Zend Framework aliases
        'aliases'   => [
            'ZF\Apigility\Doctrine\Admin\Controller\DoctrineAutodiscovery' => 'ZF\Apigility\Doctrine\Admin\Controller\DoctrineAutodiscovery',
        ],
        'factories' => [
            'ZF\Apigility\Doctrine\Admin\Controller\DoctrineAutodiscovery' => Controller\DoctrineAutodiscoveryControllerFactory::class,
        ],
    ],
    'api-tools-content-negotiation' => [
        'controllers'            => [
            'ZF\Apigility\Doctrine\Admin\Controller\DoctrineAutodiscovery'   => 'Json',
            'ZF\Apigility\Doctrine\Admin\Controller\DoctrineRestService'     => 'HalJson',
            'ZF\Apigility\Doctrine\Admin\Controller\DoctrineRpcService'      => 'HalJson',
            'ZF\Apigility\Doctrine\Admin\Controller\DoctrineMetadataService' => 'HalJson',
        ],
        'accept_whitelist'       => [
            'ZF\Apigility\Doctrine\Admin\Controller\DoctrineAutodiscovery'   => [
                'application/json',
                'application/*+json',
            ],
            'ZF\Apigility\Doctrine\Admin\Controller\DoctrineRpcService'      => [
                'application/json',
                'application/*+json',
            ],
            'ZF\Apigility\Doctrine\Admin\Controller\DoctrineRestService'     => [
                'application/json',
                'application/*+json',
            ],
            'ZF\Apigility\Doctrine\Admin\Controller\DoctrineMetadataService' => [
                'application/json',
                'application/*+json',
            ],
        ],
        'content_type_whitelist' => [
            'ZF\Apigility\Doctrine\Admin\Controller\DoctrineAutodiscovery'   => [
                'application/json',
                'application/*+json',
            ],
            'ZF\Apigility\Doctrine\Admin\Controller\DoctrineRpcService'      => [
                'application/json',
                'application/*+json',
            ],
            'ZF\Apigility\Doctrine\Admin\Controller\DoctrineRestService'     => [
                'application/json',
                'application/*+json',
            ],
            'ZF\Apigility\Doctrine\Admin\Controller\DoctrineMetadataService' => [
                'application/json',
                'application/*+json',
            ],
        ],
    ],
    'api-tools-hal'                 => [
        'metadata_map' => [
            Model\DoctrineRpcServiceEntity::class      => [
                'hydrator'               => 'ArraySerializable',
                'route_identifier_name'  => 'controller_service_name',
                'entity_identifier_name' => 'controller_service_name',
                'route_name'             => 'api-tools-doctrine-rpc-service',
            ],
            Model\DoctrineRestServiceEntity::class     => [
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
    'api-tools-rest'                => [
        'ZF\Apigility\Doctrine\Admin\Controller\DoctrineRpcService'      => [
            'listener'                   => DoctrineRpcServiceResource::class,
            'route_name'                 => 'api-tools-doctrine-rpc-service',
            'entity_class'               => Model\DoctrineRpcServiceEntity::class,
            'route_identifier_name'      => 'controller_service_name',
            'entity_http_methods'        => ['GET', 'POST', 'PATCH', 'DELETE'],
            'collection_http_methods'    => ['GET', 'POST'],
            'collection_name'            => 'doctrine-rpc',
            'collection_query_whitelist' => ['version'],
        ],
        'ZF\Apigility\Doctrine\Admin\Controller\DoctrineRestService'     => [
            'listener'                   => DoctrineRestServiceResource::class,
            'route_name'                 => 'api-tools-doctrine-service',
            'entity_class'               => Model\DoctrineRestServiceEntity::class,
            'route_identifier_name'      => 'controller_service_name',
            'entity_http_methods'        => ['GET', 'POST', 'PATCH', 'DELETE'],
            'collection_http_methods'    => ['GET', 'POST'],
            'collection_name'            => 'doctrine',
            'collection_query_whitelist' => ['version'],
        ],
        'ZF\Apigility\Doctrine\Admin\Controller\DoctrineMetadataService' => [
            'listener'                   => DoctrineMetadataServiceResource::class,
            'route_name'                 => 'api-tools-doctrine-metadata-service',
            'entity_class'               => Model\DoctrineMetadataServiceEntity::class,
            'route_identifier_name'      => 'name',
            'entity_http_methods'        => ['GET'],
            'collection_http_methods'    => ['GET'],
            'collection_name'            => 'doctrine-metadata',
            'collection_query_whitelist' => ['version'],
        ],
    ],
    'api-tools-rpc'                 => [
        'ZF\Apigility\Doctrine\Admin\Controller\DoctrineAutodiscovery' => [
            'http_methods' => ['GET'],
            'route_name'   => 'api-tools-doctrine-autodiscovery',
        ],
    ],
    'validator_metadata'            => [
        'ZF\Apigility\Doctrine\Server\Validator\ObjectExists'   => [
            'entity_class' => 'string',
            'fields'       => 'string',
        ],
        'ZF\Apigility\Doctrine\Server\Validator\NoObjectExists' => [
            'entity_class' => 'string',
            'fields'       => 'string',
        ],
    ],
];
