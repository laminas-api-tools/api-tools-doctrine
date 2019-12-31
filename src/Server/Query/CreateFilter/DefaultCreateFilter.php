<?php

namespace Laminas\ApiTools\Doctrine\Server\Query\CreateFilter;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\Doctrine\Server\Query\CreateFilter\QueryCreateFilterInterface;
use Laminas\ApiTools\Rest\ResourceEvent;

/**
 * Class DefaultCreateFilter
 *
 * @package Laminas\ApiTools\Doctrine\Server\Query\CreateFilter
 */
class DefaultCreateFilter implements ObjectManagerAwareInterface, QueryCreateFilterInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Set the object manager
     *
     * @param ObjectManager $objectManager
     */
    public function setObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Get the object manager
     *
     * @return ObjectManager
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }

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
