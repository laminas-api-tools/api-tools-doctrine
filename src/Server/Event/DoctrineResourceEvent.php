<?php

namespace Laminas\ApiTools\Doctrine\Server\Event;

use Laminas\ApiTools\Rest\ResourceEvent;
use Laminas\EventManager\Event;

/**
 * Class DoctrineResourceEvent
 *
 * @package Laminas\ApiTools\Doctrine\Server\Event
 */
class DoctrineResourceEvent extends Event
{

    const EVENT_FETCH_POST = 'fetch.post';
    const EVENT_FETCH_ALL_POST = 'fetch-all.post';
    const EVENT_CREATE_PRE = 'create.pre';
    const EVENT_CREATE_POST = 'create.post';
    const EVENT_UPDATE_PRE = 'update.pre';
    const EVENT_UPDATE_POST = 'update.post';
    const EVENT_PATCH_PRE = 'patch.pre';
    const EVENT_PATCH_POST = 'patch.post';
    const EVENT_DELETE_PRE = 'delete.pre';
    const EVENT_DELETE_POST = 'delete.post';

    /**
     * @var ResourceEvent
     */
    protected $resourceEvent;

    /**
     * @var mixed
     */
    protected $entity;

    /**
     * @var mixed
     */
    protected $collection;

    /**
     * @param mixed $collection
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return mixed
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @param mixed $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param \Laminas\ApiTools\Rest\ResourceEvent $resourceEvent
     */
    public function setResourceEvent($resourceEvent)
    {
        $this->resourceEvent = $resourceEvent;
    }

    /**
     * @return \Laminas\ApiTools\Rest\ResourceEvent
     */
    public function getResourceEvent()
    {
        return $this->resourceEvent;
    }

}
