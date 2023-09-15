<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Server\Query\Provider;

use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ORM\QueryBuilder;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\ApiTools\Doctrine\Server\Paginator\Adapter\DoctrineOrmAdapter;
use Laminas\ApiTools\Rest\ResourceEvent;
use Laminas\Paginator\Adapter\AdapterInterface;

abstract class AbstractQueryProvider implements ObjectManagerAwareInterface, QueryProviderInterface
{
    use ProvidesObjectManager;

    /**
     * @param string $entityClass
     * @param array $parameters
     * @return mixed This will return an ORM or ODM Query\Builder
     */
    abstract public function createQuery(ResourceEvent $event, $entityClass, $parameters);

    /**
     * @param QueryBuilder|Builder $queryBuilder
     * @return AdapterInterface
     */
    public function getPaginatedQuery($queryBuilder)
    {
        return new DoctrineOrmAdapter($queryBuilder->getQuery(), false);
    }

    /**
     * @param string $entityClass
     * @return int
     */
    public function getCollectionTotal($entityClass)
    {
        $queryBuilder   = $this->getObjectManager()->createQueryBuilder();
        $cmf            = $this->getObjectManager()->getMetadataFactory();
        $entityMetaData = $cmf->getMetadataFor($entityClass);

        $identifier = $entityMetaData->getIdentifier();
        $queryBuilder
            ->select('count(row.' . $identifier[0] . ')')
            ->from($entityClass, 'row');

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }
}
