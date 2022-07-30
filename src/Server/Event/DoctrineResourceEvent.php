<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Server\Event;

use Doctrine\Persistence\ObjectManager;
use Laminas\ApiTools\Rest\ResourceEvent;
use Laminas\EventManager\Event;

class DoctrineResourceEvent extends Event
{
    public const EVENT_FETCH_PRE        = 'fetch.pre';
    public const EVENT_FETCH_POST       = 'fetch.post';
    public const EVENT_FETCH_ALL_PRE    = 'fetch-all.pre';
    public const EVENT_FETCH_ALL_POST   = 'fetch-all.post';
    public const EVENT_CREATE_PRE       = 'create.pre';
    public const EVENT_CREATE_POST      = 'create.post';
    public const EVENT_UPDATE_PRE       = 'update.pre';
    public const EVENT_UPDATE_POST      = 'update.post';
    public const EVENT_PATCH_PRE        = 'patch.pre';
    public const EVENT_PATCH_POST       = 'patch.post';
    public const EVENT_PATCH_LIST_PRE   = 'patch-list.pre';
    public const EVENT_PATCH_LIST_POST  = 'patch-list.post';
    public const EVENT_DELETE_PRE       = 'delete.pre';
    public const EVENT_DELETE_POST      = 'delete.post';
    public const EVENT_DELETE_LIST_PRE  = 'delete-list.pre';
    public const EVENT_DELETE_LIST_POST = 'delete-list.post';

    /** @var ResourceEvent */
    protected $resourceEvent;

    /** @var mixed */
    protected $entity;

    /** @var mixed */
    protected $collection;

    /** @var array|mixed Should be the original data that was supplied the resource */
    protected $data;

    /** @var string */
    protected $entityClassName;

    /** @var string */
    protected $entityId;

    /** @var ObjectManager */
    protected $objectManager;

    public function getObjectManager(): ObjectManager
    {
        return $this->objectManager;
    }

    public function setObjectManager(ObjectManager $objectManager): DoctrineResourceEvent
    {
        $this->objectManager = $objectManager;

        return $this;
    }

    /**
     * @deprecated Should almost certainly be null at all times as of commit b1cf74e
     *
     * @return mixed
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @deprecated Callers have been removed in Commit b1cf74e
     *
     * @param mixed $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param mixed $entity
     * @return $this
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data The Original Data supplied to the Resource Method
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return ResourceEvent
     */
    public function getResourceEvent()
    {
        return $this->resourceEvent;
    }

    /**
     * @param ResourceEvent $resourceEvent
     * @return $this
     */
    public function setResourceEvent($resourceEvent)
    {
        $this->resourceEvent = $resourceEvent;

        return $this;
    }

    /**
     * @return string
     */
    public function getEntityClassName()
    {
        return $this->entityClassName;
    }

    /**
     * @param string $entityClassName
     * @return $this
     */
    public function setEntityClassName($entityClassName)
    {
        $this->entityClassName = $entityClassName;
        return $this;
    }

    /**
     * @return string
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * @param string $entityId
     * @return $this
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;

        return $this;
    }
}
