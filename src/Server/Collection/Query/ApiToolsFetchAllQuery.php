<?php

namespace Laminas\ApiTools\Doctrine\Server\Collection\Query;

use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Laminas\Paginator\Adapter\AdapterInterface;
use Laminas\ServiceManager\AbstractPluginManager;

interface ApiToolsFetchAllQuery extends ObjectManagerAwareInterface
{
    public function setFilterManager(AbstractPluginManager $filterManager);
    public function getFilterManager();

    /**
     * @param string $entityClass
     * @param array  $parameters
     *
     * @return mixed This will return an ORM or ODM Query\Builder
     */
    public function createQuery($entityClass, $parameters);

    /**
     * @param   $queryBuilder
     *
     * @return AdapterInterface
     */
    public function getPaginatedQuery($queryBuilder);

    /**
     * @param   $entityClass
     *
     * @return int
     */
    public function getCollectionTotal($entityClass);

}
