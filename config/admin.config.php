<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-doctrine for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/LICENSE.md New BSD License
 */

return array(
    'router' => array(
        'routes' => array(
            'api-tools-doctrine-rpc-service' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api-tools/api/module[/:name]/doctrine-rpc[/:controller_service_name]',
                    'defaults' => array(
                        'controller' => 'Laminas\ApiTools\Doctrine\Admin\Controller\DoctrineRpcService',
                    ),
                ),
                'may_terminate' => true,
            ),
            'api-tools-doctrine-service' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api-tools/api/module[/:name]/doctrine[/:controller_service_name]',
                    'defaults' => array(
                        'controller' => 'Laminas\ApiTools\Doctrine\Admin\Controller\DoctrineRestService',
                    ),
                ),
                'may_terminate' => true,
            ),
            'api-tools-doctrine-metadata-service' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api-tools/api/doctrine[/:object_manager_alias]/metadata[/:name]',
                    'defaults' => array(
                        'controller' => 'Laminas\ApiTools\Doctrine\Admin\Controller\DoctrineMetadataService',
                    ),
                ),
                'may_terminate' => true,
            ),
        ),
    ),

    'api-tools-content-negotiation' => array(
        'controllers' => array(
            'Laminas\ApiTools\Doctrine\Admin\Controller\DoctrineRestService' => 'HalJson',
            'Laminas\ApiTools\Doctrine\Admin\Controller\DoctrineRpcService' => 'HalJson',
            'Laminas\ApiTools\Doctrine\Admin\Controller\DoctrineMetadataService' => 'HalJson',
        ),
        'accept-whitelist' => array(
            'Laminas\ApiTools\Doctrine\Admin\Controller\DoctrineRpcService' => array(
                'application/json',
                'application/*+json',
            ),
            'Laminas\ApiTools\Doctrine\Admin\Controller\DoctrineRestService' => array(
                'application/json',
                'application/*+json',
            ),
            'Laminas\ApiTools\Doctrine\Admin\Controller\DoctrineMetadataService' => array(
                'application/json',
                'application/*+json',
            ),
        ),
        'content-type-whitelist' => array(
            'Laminas\ApiTools\Doctrine\Admin\Controller\DoctrineRpcService' => array(
                'application/json',
                'application/*+json',
            ),
            'Laminas\ApiTools\Doctrine\Admin\Controller\DoctrineRestService' => array(
                'application/json',
                'application/*+json',
            ),
            'Laminas\ApiTools\Doctrine\Admin\Controller\DoctrineMetadataService' => array(
                'application/json',
                'application/*+json',
            ),
        ),
    ),

    'api-tools-hal' => array(
        'metadata_map' => array(
            'Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRpcServiceEntity' => array(
                'hydrator'        => 'ArraySerializable',
                'route_identifier_name' => 'controller_service_name',
                'entity_identifier_name' => 'controller_service_name',
                'route_name'      => 'api-tools-doctrine-rpc-service',
            ),
            'Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceEntity' => array(
                'hydrator'        => 'ArraySerializable',
                'route_identifier_name' => 'controller_service_name',
                'entity_identifier_name' => 'controller_service_name',
                'route_name'      => 'api-tools-doctrine-service',
            ),
            'Laminas\ApiTools\Doctrine\Admin\Model\DoctrineMetadataServiceEntity' => array(
                'hydrator'        => 'ArraySerializable',
                'entity_identifier_name' => 'name',
                'route_identifier_name'      => 'name',
                'route_name'      => 'api-tools-doctrine-metadata-service',
            ),
        ),
    ),

    'api-tools-rest' => array(
        'Laminas\ApiTools\Doctrine\Admin\Controller\DoctrineRpcService' => array(
            'listener'                   => 'Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRpcServiceResource',
            'route_name'                 => 'api-tools-doctrine-rpc-service',
            'entity_class'               => 'Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRpcServiceEntity',
            'route_identifier_name'      => 'controller_service_name',
            'entity_http_methods'      => array('GET', 'POST', 'PATCH', 'DELETE'),
            'collection_http_methods'    => array('GET', 'POST'),
            'collection_name'            => 'doctrine-rpc',
            'collection_query_whitelist' => array('version'),
        ),
        'Laminas\ApiTools\Doctrine\Admin\Controller\DoctrineRestService' => array(
            'listener'                   => 'Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceResource',
            'route_name'                 => 'api-tools-doctrine-service',
            'entity_class'               => 'Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceEntity',
            'route_identifier_name'      => 'controller_service_name',
            'entity_http_methods'      => array('GET', 'POST', 'PATCH', 'DELETE'),
            'collection_http_methods'    => array('GET', 'POST'),
            'collection_name'            => 'doctrine',
            'collection_query_whitelist' => array('version'),
        ),
        'Laminas\ApiTools\Doctrine\Admin\Controller\DoctrineMetadataService' => array(
            'listener'                   => 'Laminas\ApiTools\Doctrine\Admin\Model\DoctrineMetadataServiceResource',
            'route_name'                 => 'api-tools-doctrine-metadata-service',
            'entity_class'               => 'Laminas\ApiTools\Doctrine\Admin\Model\DoctrineMetadataServiceEntity',
            'route_identifier_name'      => 'name',
            'entity_http_methods'      => array('GET'),
            'collection_http_methods'    => array('GET'),
            'collection_name'            => 'doctrine-metadata',
            'collection_query_whitelist' => array('version'),
        ),
    ),
    'validator_metadata' => array(
        'Laminas\ApiTools\Doctrine\Server\Validator\ObjectExists' => array(
            'entity_class' => 'string',
            'fields' => 'string',
        ),
        'Laminas\ApiTools\Doctrine\Server\Validator\NoObjectExists' => array(
            'entity_class' => 'string',
            'fields' => 'string',
        ),
    ),
);
