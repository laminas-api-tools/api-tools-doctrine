<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Server\Query\CreateFilter;

use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\ApiTools\Rest\ResourceEvent;

abstract class AbstractCreateFilter implements ObjectManagerAwareInterface, QueryCreateFilterInterface
{
    use ProvidesObjectManager;

    /**
     * @param string $entityClass
     * @param array $data
     * @return array
     */
    abstract public function filter(ResourceEvent $event, $entityClass, $data);
}
