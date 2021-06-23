<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Server\Paginator\Adapter;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Laminas\Paginator\Adapter\AdapterInterface;

use function array_key_exists;

class DoctrineOrmAdapter extends Paginator implements AdapterInterface
{
    /** @var array */
    public $cache = [];

    /**
     * @param int $offset
     * @param int $itemCountPerPage
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        if (
            array_key_exists($offset, $this->cache)
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
