<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-doctrine for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/LICENSE.md New BSD License
 */

return array(
    'service_manager' => array(
        'invokables' => array(
            'Laminas\\ApiTools\\Doctrine\\Server\\Hydrator\\Strategy\\CollectionLink' => 'Laminas\\ApiTools\\Doctrine\\Server\\Hydrator\\Strategy\\CollectionLink',
            'Laminas\\ApiTools\\Doctrine\\Server\\Hydrator\\Strategy\\CollectionExtract' => 'Laminas\\ApiTools\\Doctrine\\Server\\Hydrator\\Strategy\\CollectionExtract',
        ),
        'abstract_factories' => array(
            'Laminas\ApiTools\Doctrine\Server\Resource\DoctrineResourceFactory',
        ),
        'factories' => array(
            'LaminasOrmCollectionFilterManager' => 'Laminas\ApiTools\Doctrine\Server\Collection\Service\ORMFilterManagerFactory',
            'LaminasOdmCollectionFilterManager' => 'Laminas\ApiTools\Doctrine\Server\Collection\Service\ODMFilterManagerFactory',
            'LaminasCollectionQueryManager' => 'Laminas\ApiTools\Doctrine\Server\Collection\Service\QueryManagerFactory',
        ),
    ),

    'api-tools-collection-query' => array(
        'invokables' => array(
            'default-orm-query' => 'Laminas\ApiTools\Doctrine\Server\Collection\Query\FetchAllOrmQuery',
            'default-odm-query' => 'Laminas\ApiTools\Doctrine\Server\Collection\Query\FetchAllOdmQuery',
        )
    ),

    'api-tools-orm-collection-filter' => array(
        'invokables' => array(
            'eq' => 'Laminas\ApiTools\Doctrine\Server\Collection\Filter\ORM\Equals',
            'neq' => 'Laminas\ApiTools\Doctrine\Server\Collection\Filter\ORM\NotEquals',
            'lt' => 'Laminas\ApiTools\Doctrine\Server\Collection\Filter\ORM\LessThan',
            'lte' => 'Laminas\ApiTools\Doctrine\Server\Collection\Filter\ORM\LessThanOrEquals',
            'gt' => 'Laminas\ApiTools\Doctrine\Server\Collection\Filter\ORM\GreaterThan',
            'gte' => 'Laminas\ApiTools\Doctrine\Server\Collection\Filter\ORM\GreaterThanOrEquals',
            'isnull' => 'Laminas\ApiTools\Doctrine\Server\Collection\Filter\ORM\IsNull',
            'isnotnull' => 'Laminas\ApiTools\Doctrine\Server\Collection\Filter\ORM\IsNotNull',
            'in' => 'Laminas\ApiTools\Doctrine\Server\Collection\Filter\ORM\In',
            'notin' => 'Laminas\ApiTools\Doctrine\Server\Collection\Filter\ORM\NotIn',
            'between' => 'Laminas\ApiTools\Doctrine\Server\Collection\Filter\ORM\Between',
            'like' => 'Laminas\ApiTools\Doctrine\Server\Collection\Filter\ORM\Like',
            'notlike' => 'Laminas\ApiTools\Doctrine\Server\Collection\Filter\ORM\NotLike',
            'orx' => 'Laminas\ApiTools\Doctrine\Server\Collection\Filter\ORM\OrX',
            'andx' => 'Laminas\ApiTools\Doctrine\Server\Collection\Filter\ORM\AndX',
        ),
    ),

    'api-tools-odm-collection-filter' => array(
        'invokables' => array(
            'eq' => 'Laminas\ApiTools\Doctrine\Server\Collection\Filter\ODM\Equals',
            'neq' => 'Laminas\ApiTools\Doctrine\Server\Collection\Filter\ODM\NotEquals',
            'lt' => 'Laminas\ApiTools\Doctrine\Server\Collection\Filter\ODM\LessThan',
            'lte' => 'Laminas\ApiTools\Doctrine\Server\Collection\Filter\ODM\LessThanOrEquals',
            'gt' => 'Laminas\ApiTools\Doctrine\Server\Collection\Filter\ODM\GreaterThan',
            'gte' => 'Laminas\ApiTools\Doctrine\Server\Collection\Filter\ODM\GreaterThanOrEquals',
            'isnull' => 'Laminas\ApiTools\Doctrine\Server\Collection\Filter\ODM\IsNull',
            'isnotnull' => 'Laminas\ApiTools\Doctrine\Server\Collection\Filter\ODM\IsNotNull',
            'in' => 'Laminas\ApiTools\Doctrine\Server\Collection\Filter\ODM\In',
            'notin' => 'Laminas\ApiTools\Doctrine\Server\Collection\Filter\ODM\NotIn',
            'between' => 'Laminas\ApiTools\Doctrine\Server\Collection\Filter\ODM\Between',
            'like' => 'Laminas\ApiTools\Doctrine\Server\Collection\Filter\ODM\Like',
            'regex' => 'Laminas\ApiTools\Doctrine\Server\Collection\Filter\ODM\Regex',
        ),
    ),

    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                __DIR__ . '/../asset',
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'api-tools-doctrine-server' => __DIR__ . '/../view',
        ),
    ),
);
