<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-doctrine for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\Doctrine\Server\Paginator\Adapter;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Laminas\Paginator\Adapter\AdapterInterface;

class DoctrineOrmAdapter extends Paginator implements AdapterInterface
{
    /**
     * @var array
    */
    public $cache = [];

    /**
     * @param $offset
     * @param $itemCountPerPage
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
            $this->cache[$offset] = [];
        }

        $this->cache[$offset][$itemCountPerPage] = $this->getQuery()->getResult();

        return $this->cache[$offset][$itemCountPerPage];
    }
}
