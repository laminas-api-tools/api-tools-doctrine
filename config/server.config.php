<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-doctrine for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/LICENSE.md New BSD License
 */

return array(
    'service_manager' => array(
        'invokables' => array(
            'Laminas\\ApiTools\\Doctrine\\Server\\Hydrator\\Strategy\\CollectionExtract' =>
                'Laminas\\ApiTools\\Doctrine\\Server\\Hydrator\\Strategy\\CollectionExtract',
        ),
        'abstract_factories' => array(
            'Laminas\ApiTools\Doctrine\Server\Resource\DoctrineResourceFactory',
        ),
        'factories' => array(
            'LaminasApiToolsDoctrineQueryProviderManager' =>
                'Laminas\ApiTools\Doctrine\Server\Query\Provider\Service\QueryProviderManagerFactory',
        ),
    ),

    'api-tools-doctrine-query-provider' => array(
        'invokables' => array(
            'default_orm' => 'Laminas\ApiTools\Doctrine\Server\Query\Provider\DefaultOrm',
            'default_odm' => 'Laminas\ApiTools\Doctrine\Server\Query\Provider\DefaultOdm',
        )
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'api-tools-doctrine' => __DIR__ . '/../view',
        ),
    ),

    'validators' => array(
        'factories' => array(
            'Laminas\ApiTools\Doctrine\Server\Validator\NoObjectExists' =>
                'Laminas\ApiTools\Doctrine\Server\Validator\NoObjectExistsFactory',
            'Laminas\ApiTools\Doctrine\Server\Validator\ObjectExists' =>
                'Laminas\ApiTools\Doctrine\Server\Validator\ObjectExistsFactory',
        ),
    ),
);
