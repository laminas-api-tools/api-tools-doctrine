<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Server\Query\Provider;

use Doctrine\Odm\MongoDB\Query\Builder;
use Laminas\ApiTools\Doctrine\Server\Paginator\Adapter\DoctrineOdmAdapter;
use Laminas\ApiTools\Rest\ResourceEvent;

class DefaultOdm extends AbstractQueryProvider
{
    /**
     * {@inheritDoc}
     */
    public function createQuery(ResourceEvent $event, $entityClass, $parameters)
    {
        /** @var Builder $queryBuilder */
        $queryBuilder = $this->getObjectManager()->createQueryBuilder();
        $queryBuilder->find($entityClass);

        return $queryBuilder;
    }

    /**
     * @param Builder $queryBuilder
     * @return DoctrineOdmAdapter
     */
    public function getPaginatedQuery($queryBuilder)
    {
        return new DoctrineOdmAdapter($queryBuilder);
    }

    /**
     * @param class-string $entityClass
     * @return int
     */
    public function getCollectionTotal($entityClass)
    {
        $queryBuilder = $this->getObjectManager()->createQueryBuilder();
        $queryBuilder->find($entityClass);
        return $queryBuilder->getQuery()->execute()->count();
    }
}
