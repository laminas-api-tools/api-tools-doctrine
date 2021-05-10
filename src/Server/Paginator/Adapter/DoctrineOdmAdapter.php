<?php

namespace Laminas\ApiTools\Doctrine\Server\Paginator\Adapter;

use Doctrine\Odm\MongoDB\Query\Builder;
use Laminas\Paginator\Adapter\AdapterInterface;

class DoctrineOdmAdapter implements AdapterInterface
{
    /**
     * @var Builder $queryBuilder
     */
    protected $queryBuilder;

    /**
     * @param Builder $queryBuilder
     */
    public function __construct($queryBuilder)
    {
        $this->setQueryBuilder($queryBuilder);
    }

    /**
     * @param \Doctrine\Odm\MongoDB\Query\Builder $queryBuilder
     */
    public function setQueryBuilder($queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @return \Doctrine\Odm\MongoDB\Query\Builder
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    /**
     * @param $offset
     * @param $itemCountPerPage
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder->skip($offset);
        $queryBuilder->limit($itemCountPerPage);

        return $queryBuilder->getQuery()->execute()->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        $queryBuilder = clone $this->getQueryBuilder();
        $queryBuilder->skip(0);
        $queryBuilder->limit(null);

        return $queryBuilder->getQuery()->execute()->count();
    }
}
