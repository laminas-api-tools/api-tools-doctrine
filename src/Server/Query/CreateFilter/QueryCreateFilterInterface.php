<?php

namespace Laminas\ApiTools\Doctrine\Server\Query\CreateFilter;

use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Laminas\ApiTools\Rest\ResourceEvent;

interface QueryCreateFilterInterface extends ObjectManagerAwareInterface
{
    /**
     * @param ResourceEvent $event
     * @param string $entityClass
     * @param array $data
     * @return array
     */
    public function filter(ResourceEvent $event, $entityClass, $data);
}
