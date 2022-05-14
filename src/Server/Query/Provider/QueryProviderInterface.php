<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Server\Query\Provider;

use Doctrine\ORM\QueryBuilder;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Laminas\ApiTools\Rest\ResourceEvent;
use Laminas\Paginator\Adapter\AdapterInterface;

interface QueryProviderInterface extends ObjectManagerAwareInterface
{
    /**
     * @param string $entityClass
     * @param array $parameters
     * @return mixed This will return an ORM QueryBuilder
     */
    public function createQuery(ResourceEvent $event, $entityClass, $parameters);

    /**
     * This function is not necessary for any but fetch-all queries
     * In order to provide a single QueryProvider service this is
     * included in this interface.
     *
     * @param QueryBuilder $queryBuilder
     * @return AdapterInterface
     */
    public function getPaginatedQuery($queryBuilder);

    /**
     * This function is not necessary for any but fetch-all queries
     * In order to provide a single QueryProvider service this is
     * included in this interface.
     *
     * @param string $entityClass
     * @return int
     */
    public function getCollectionTotal($entityClass);
}
