<?php

namespace Laminas\ApiTools\Doctrine\Server\Paginator\Adapter;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Laminas\Paginator\Adapter\AdapterInterface;

/**
 * Class DoctrineOrmAdapter
 *
 * @package Laminas\ApiTools\Doctrine\Server\Paginator\Adapter
 */
class DoctrineOrmAdapter extends Paginator implements AdapterInterface
{
    /**
     * @var array
    */
    public $cache = array();

    /**
     * @param $offset
     * @param $itemCountPerPage
     *
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        if (array_key_exists($offset, $this->cache)
            && array_key_exists($itemCountPerPage, $this->cache[$offset])
        ) {
            return $this->cache[$offset][$itemCountPerPage];
        }

        $this->getQuery()->setFirstResult($offset);
        $this->getQuery()->setMaxResults($itemCountPerPage);

        if (! array_key_exists($offset, $this->cache)) {
            $this->cache[$offset] = array();
        }

        $this->cache[$offset][$itemCountPerPage] =  $this->getQuery()->getResult();

        return $this->cache[$offset][$itemCountPerPage];
    }
}
