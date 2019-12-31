<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-doctrine for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\Doctrine\Server\Query\Provider;

use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Laminas\ApiTools\Rest\ResourceEvent;
use Laminas\Paginator\Adapter\AdapterInterface;

interface QueryProviderInterface extends ObjectManagerAwareInterface
{
    /**
     * @param ResourceEvent $event
     * @param string $entityClass
     * @param array $parameters
     * @return mixed This will return an ORM or ODM Query\Builder
     */
    public function createQuery(ResourceEvent $event, $entityClass, $parameters);

    /**
     * This function is not necessary for any but fetch-all queries
     * In order to provide a single QueryProvider service this is
     * included in this interface.
     *
     * @param $queryBuilder
     * @return AdapterInterface
     */
    public function getPaginatedQuery($queryBuilder);

    /**
     * This function is not necessary for any but fetch-all queries
     * In order to provide a single QueryProvider service this is
     * included in this interface.
     *
     * @param $entityClass
     * @return int
     */
    public function getCollectionTotal($entityClass);
}
