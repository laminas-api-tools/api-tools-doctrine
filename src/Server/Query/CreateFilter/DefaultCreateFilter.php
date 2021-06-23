<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Server\Query\CreateFilter;

use Laminas\ApiTools\Rest\ResourceEvent;

class DefaultCreateFilter extends AbstractCreateFilter
{
    /**
     * @param string $entityClass
     * @param array $data
     * @return array
     */
    public function filter(ResourceEvent $event, $entityClass, $data)
    {
        return $data;
    }
}
