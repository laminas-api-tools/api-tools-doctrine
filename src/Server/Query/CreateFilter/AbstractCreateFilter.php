<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Server\Query\CreateFilter;

use Doctrine\Persistence\ObjectManager;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Laminas\ApiTools\Rest\ResourceEvent;

abstract class AbstractCreateFilter implements ObjectManagerAwareInterface, QueryCreateFilterInterface
{
    /**
     * @param string $entityClass
     * @param array $data
     * @return array
     */
    abstract public function filter(ResourceEvent $event, $entityClass, $data);

    /** @var ObjectManager */
    protected $objectManager;

    /**
     * Set the object manager
     *
     * @return void
     */
    public function setObjectManager(ObjectManager $objectManager): void
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Get the object manager
     *
     * @return ObjectManager
     */
    public function getObjectManager(): ObjectManager
    {
        return $this->objectManager;
    }
}
