<?php

namespace Laminas\ApiTools\Doctrine\Server\Collection\Service;

use Laminas\ApiTools\Doctrine\Server\Collection\Query\ApiToolsFetchAllQuery;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Exception;

class QueryManager extends AbstractPluginManager
{
    protected $invokableClasses = array();

    /**
     * @param mixed $filter
     *
     * @return void
     * @throws Exception\RuntimeException
     */
    public function validatePlugin($filter)
    {
        if ($filter instanceof ApiToolsFetchAllQuery) {
            // we're okay
            return;
        }

        // @codeCoverageIgnoreStart
        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement ApiToolsFetchAllQuery',
            (is_object($filter) ? get_class($filter) : gettype($filter))
        ));
        // @codeCoverageIgnoreEnd
    }
}
