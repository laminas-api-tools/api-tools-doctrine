<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Server\Query\CreateFilter;

use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Laminas\ApiTools\Rest\ResourceEvent;

interface QueryCreateFilterInterface extends ObjectManagerAwareInterface
{
    /**
     * @param string $entityClass
     * @param array $data
     * @return array
     */
    public function filter(ResourceEvent $event, $entityClass, $data);
}
