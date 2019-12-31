<?php

namespace Laminas\ApiTools\Doctrine\Server\Query\CreateFilter;

use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\Rest\ResourceEvent;

/**
 * Class DefaultCreateFilter
 *
 * @package Laminas\ApiTools\Doctrine\Server\Query\CreateFilter
 */
class DefaultCreateFilter extends AbstractCreateFilter
{
    /**
     * @param string $entityClass
     * @param array  $data
     *
     * @return array
     */
    public function filter(ResourceEvent $event, $entityClass, $data)
    {
        return $data;
    }
}
