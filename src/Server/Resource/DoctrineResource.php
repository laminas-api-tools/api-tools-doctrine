<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Server\Resource;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Instantiator\InstantiatorInterface;
use Doctrine\ODM\MongoDB\Query\Builder as MongoDBQueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Stdlib\Hydrator;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\Doctrine\Server\Event\DoctrineResourceEvent;
use Laminas\ApiTools\Doctrine\Server\Exception\InvalidArgumentException;
use Laminas\ApiTools\Doctrine\Server\Query\CreateFilter\QueryCreateFilterInterface;
use Laminas\ApiTools\Doctrine\Server\Query\Provider\QueryProviderInterface;
use Laminas\ApiTools\Hal\Collection;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\ApiTools\Rest\RestController;
use Laminas\EventManager\EventInterface;
use Laminas\EventManager\EventManager;
use Laminas\EventManager\EventManagerAwareInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ResponseCollection;
use Laminas\EventManager\SharedEventManager;
use Laminas\Hydrator\HydratorAwareInterface;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Mvc\ModuleRouteListener;
use ReflectionClass;
use Traversable;

use function array_diff_key;
use function array_flip;
use function array_key_exists;
use function array_merge;
use function array_unique;
use function count;
use function explode;
use function in_array;
use function is_array;
use function is_object;
use function is_string;
use function md5;
use function method_exists;
use function rand;

class DoctrineResource extends AbstractResourceListener implements
    ObjectManagerAwareInterface,
    EventManagerAwareInterface,
    HydratorAwareInterface
{
    /** @var SharedEventManager Interface */
    protected $sharedEventManager;

    /** @var ObjectManager */
    protected $objectManager;

    /** @var EventManagerInterface */
    protected $events;

    /** @var array */
    protected $eventIdentifier = ['Laminas\ApiTools\Doctrine\DoctrineResource'];

    /** @var array|QueryProviderInterface */
    protected $queryProviders;

    /** @var string entityIdentifierName */
    protected $entityIdentifierName;

    /** @var string */
    protected $routeIdentifierName;

    /** @var QueryCreateFilterInterface */
    protected $queryCreateFilter;

    /** @var string */
    protected $multiKeyDelimiter = '.';

    /** @var HydratorInterface */
    protected $hydrator;

    /** @var InstantiatorInterface|null */
    private $entityFactory;

    public function __construct(?InstantiatorInterface $entityFactory = null)
    {
        $this->entityFactory = $entityFactory;
    }

    /**
     * @return SharedEventManager
     */
    public function getSharedEventManager()
    {
        return $this->sharedEventManager;
    }

    /**
     * @return $this
     */
    public function setSharedEventManager(SharedEventManager $sharedEventManager)
    {
        $this->sharedEventManager = $sharedEventManager;

        return $this;
    }

    /**
     * Set the event manager instance used by this context.
     *
     * For convenience, this method will also set the class name / LSB name as
     * identifiers, in addition to any string or array of strings set to the
     * $this->eventIdentifier property.
     *
     * @return $this
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $identifiers = [self::class, static::class];
        if (isset($this->eventIdentifier)) {
            if (
                is_string($this->eventIdentifier)
                || is_array($this->eventIdentifier)
                || $this->eventIdentifier instanceof Traversable
            ) {
                $identifiers = array_unique(array_merge($identifiers, (array) $this->eventIdentifier));
            } elseif (is_object($this->eventIdentifier)) {
                $identifiers[] = $this->eventIdentifier;
            }
            // silently ignore invalid eventIdentifier types
        }
        $events->setIdentifiers($identifiers);
        $this->events = $events;
        if (method_exists($this, 'attachDefaultListeners')) {
            $this->attachDefaultListeners();
        }

        return $this;
    }

    /**
     * Retrieve the event manager
     *
     * Lazy-loads an EventManager instance if none registered.
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        if (! $this->events instanceof EventManagerInterface) {
            $this->setEventManager(new EventManager($this->getSharedEventManager()));
        }

        return $this->events;
    }

    /**
     * Set the object manager
     *
     * @return void
     */
    public function setObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Get the object manager
     *
     * @return ObjectManager|EntityManagerInterface
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }

    /**
     * @param array|QueryProviderInterface[] $queryProviders
     * @return void
     * @throws InvalidArgumentException If parameter is not an array or \Traversable object.
     */
    public function setQueryProviders($queryProviders)
    {
        if (! is_array($queryProviders) && ! $queryProviders instanceof Traversable) {
            throw new InvalidArgumentException('queryProviders must be array or Traversable object');
        }

        foreach ($queryProviders as $qp) {
            if (! $qp instanceof QueryProviderInterface) {
                throw new InvalidArgumentException('queryProviders must implement QueryProviderInterface');
            }
        }

        $this->queryProviders = (array) $queryProviders;
    }

    /**
     * @return QueryProviderInterface|array
     */
    public function getQueryProviders()
    {
        return $this->queryProviders;
    }

    /**
     * @param string $method
     * @return QueryProviderInterface
     */
    public function getQueryProvider($method)
    {
        $queryProviders = $this->getQueryProviders();

        if (isset($queryProviders[$method])) {
            return $queryProviders[$method];
        }

        return $queryProviders['default'];
    }

    /**
     * @return string
     */
    public function getEntityIdentifierName()
    {
        return $this->entityIdentifierName;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setEntityIdentifierName($value)
    {
        $this->entityIdentifierName = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getRouteIdentifierName()
    {
        return $this->routeIdentifierName;
    }

    /**
     * @param string $routeIdentifierName
     * @return $this
     */
    public function setRouteIdentifierName($routeIdentifierName)
    {
        $this->routeIdentifierName = $routeIdentifierName;
        return $this;
    }

    /**
     * @return $this
     */
    public function setQueryCreateFilter(QueryCreateFilterInterface $value)
    {
        $this->queryCreateFilter = $value;

        return $this;
    }

    /**
     * @return QueryCreateFilterInterface
     */
    public function getQueryCreateFilter()
    {
        return $this->queryCreateFilter;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setMultiKeyDelimiter($value)
    {
        $this->multiKeyDelimiter = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getMultiKeyDelimiter()
    {
        return $this->multiKeyDelimiter;
    }

    /**
     * @return $this
     */
    public function setHydrator(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;

        return $this;
    }

    /**
     * @return HydratorInterface
     */
    public function getHydrator()
    {
        if (! $this->hydrator) {
            // FIXME: find a way to test this line from a created API.  Shouldn't all created API's have a hydrator?
            $this->hydrator = new Hydrator\DoctrineObject($this->getObjectManager(), $this->getEntityClass());
        }

        return $this->hydrator;
    }

    /**
     * Create a resource
     *
     * @param mixed $data
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        $entityClass = $this->getEntityClass();

        $data = $this->getQueryCreateFilter()->filter($this->getEvent(), $entityClass, $data);
        if ($data instanceof ApiProblem) {
            return $data;
        }

        $entity = $this->entityFactory
            ? $this->entityFactory->instantiate($entityClass)
            : new $entityClass();

        $results = $this->triggerDoctrineEvent(DoctrineResourceEvent::EVENT_CREATE_PRE, $entity, $data);
        if ($results->last() instanceof ApiProblem) {
            return $results->last();
        } elseif (! $results->isEmpty() && $results->last() !== null) {
            // TODO Change to a more logical/secure way to see if data was acted and and we have the expected response
            $preEventData = $results->last();
        } else {
            $preEventData = $data;
        }

        $hydrator = $this->getHydrator();
        $hydrator->hydrate((array) $preEventData, $entity);

        $this->getObjectManager()->persist($entity);

        $results = $this->triggerDoctrineEvent(DoctrineResourceEvent::EVENT_CREATE_POST, $entity, $data);
        if ($results->last() instanceof ApiProblem) {
            return $results->last();
        }

        $this->getObjectManager()->flush();

        return $entity;
    }

    /**
     * Delete a resource
     *
     * @param mixed $id
     * @return ApiProblem|mixed
     */
    public function delete($id)
    {
        $entity = $this->findEntity($id, 'delete');

        if ($entity instanceof ApiProblem) {
            return $entity;
        }

        $results = $this->triggerDoctrineEvent(DoctrineResourceEvent::EVENT_DELETE_PRE, $entity);
        if ($results->last() instanceof ApiProblem) {
            return $results->last();
        }

        $this->getObjectManager()->remove($entity);

        $results = $this->triggerDoctrineEvent(DoctrineResourceEvent::EVENT_DELETE_POST, $entity);
        if ($results->last() instanceof ApiProblem) {
            return $results->last();
        }

        $this->getObjectManager()->flush();

        return true;
    }

    /**
     * Respond to the PATCH method (partial update of existing entity) on
     * a collection, i.e. update multiple entities in a collection.
     *
     * @param array $data
     * @return array
     */
    public function patchList($data)
    {
        $return = new ArrayCollection();

        $results = $this->triggerDoctrineEvent(DoctrineResourceEvent::EVENT_PATCH_LIST_PRE, $data, $data);
        if ($results->last() instanceof ApiProblem) {
            return $results->last();
        }

        if (! $this->getObjectManager() instanceof EntityManagerInterface) {
            throw new InvalidArgumentException('Invalid Object Manager, must implement EntityManagerInterface');
        }

        $this->getObjectManager()->getConnection()->beginTransaction();
        foreach ($data as $row) {
            $result = $this->patch($row[$this->getEntityIdentifierName()], $row);
            if ($result instanceof ApiProblem) {
                $this->getObjectManager()->getConnection()->rollback();

                return $result;
            }

            $return->add($result);
        }
        $this->getObjectManager()->getConnection()->commit();

        $results = $this->triggerDoctrineEvent(DoctrineResourceEvent::EVENT_PATCH_LIST_POST, $return, $data);
        if ($results->last() instanceof ApiProblem) {
            return $results->last();
        }

        return $return;
    }

    /**
     * Delete a list of entities
     *
     * @param mixed $data
     * @return ApiProblem|mixed
     */
    public function deleteList($data)
    {
        $results = $this->triggerDoctrineEvent(DoctrineResourceEvent::EVENT_DELETE_LIST_PRE, $data, $data);
        if ($results->last() instanceof ApiProblem) {
            return $results->last();
        }

        $this->getObjectManager()->getConnection()->beginTransaction();
        foreach ($data as $row) {
            $result = $this->delete($row[$this->getEntityIdentifierName()]);

            if ($result instanceof ApiProblem) {
                $this->getObjectManager()->getConnection()->rollback();

                return $result;
            }
        }
        $this->getObjectManager()->getConnection()->commit();

        $results = $this->triggerDoctrineEvent(DoctrineResourceEvent::EVENT_DELETE_LIST_POST, true, $data);
        if ($results->last() instanceof ApiProblem) {
            return $results->last();
        }

        return true;
    }

    /**
     * Fetch a resource
     *
     * If the extractCollections array contains a collection for this resource
     * expand that collection instead of returning a link to the collection
     *
     * @param mixed $id
     * @return ApiProblem|mixed
     */
    public function fetch($id)
    {
        $event = new DoctrineResourceEvent(DoctrineResourceEvent::EVENT_FETCH_PRE, $this);
        $event->setEntityClassName($this->getEntityClass());
        $event->setResourceEvent($this->getEvent());
        $event->setEntityId($id);
        $eventManager = $this->getEventManager();
        $response     = $eventManager->triggerEvent($event);
        if ($response->last() instanceof ApiProblem) {
            return $response->last();
        }

        $entity = $this->findEntity($id, 'fetch');

        if ($entity instanceof ApiProblem) {
            return $entity;
        }

        $results = $this->triggerDoctrineEvent(DoctrineResourceEvent::EVENT_FETCH_POST, $entity);
        if ($results->last() instanceof ApiProblem) {
            return $results->last();
        }

        return $entity;
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param array $data
     * @return ApiProblem|mixed
     */
    public function fetchAll($data = [])
    {
        // Build query
        $queryProvider = $this->getQueryProvider('fetch_all');
        $queryBuilder  = $queryProvider->createQuery($this->getEvent(), $this->getEntityClass(), $data);

        if ($queryBuilder instanceof ApiProblem) {
            return $queryBuilder;
        }

        $response = $this->triggerDoctrineEvent(
            DoctrineResourceEvent::EVENT_FETCH_ALL_PRE,
            $this->getEntityClass(),
            $data
        );
        if ($response->last() instanceof ApiProblem) {
            return $response->last();
        }

        $adapter    = $queryProvider->getPaginatedQuery($queryBuilder);
        $reflection = new ReflectionClass($this->getCollectionClass());
        $collection = $reflection->newInstance($adapter);

        $results = $this->triggerDoctrineEvent(
            DoctrineResourceEvent::EVENT_FETCH_ALL_POST,
            $this->getEntityClass(),
            $data
        );
        if ($results->last() instanceof ApiProblem) {
            return $results->last();
        }

        // Add event to set extra HAL data
        $this->getEntityClass();

        $this->getSharedEventManager()->attach(
            RestController::class,
            'getList.post',
            function (EventInterface $e) {
                /** @var Collection $halCollection */
                $halCollection = $e->getParam('collection');
                $collection    = $halCollection->getCollection();

                $collection->setItemCountPerPage($halCollection->getPageSize());
                $collection->setCurrentPageNumber($halCollection->getPage());

                $halCollection->setCollectionRouteOptions([
                    'query' => $e->getTarget()->getRequest()->getQuery()->toArray(),
                ]);
            }
        );

        return $collection;
    }

    /**
     * Patch (partial in-place update) a resource
     *
     * @param mixed $id
     * @param mixed $data
     * @return ApiProblem|mixed
     */
    public function patch($id, $data)
    {
        $entity = $this->findEntity($id, 'patch', $data);

        if ($entity instanceof ApiProblem) {
            return $entity;
        }

        $results = $this->triggerDoctrineEvent(DoctrineResourceEvent::EVENT_PATCH_PRE, $entity, $data);
        if ($results->last() instanceof ApiProblem) {
            return $results->last();
        } elseif (! $results->isEmpty() && $results->last() !== null) {
            // TODO Change to a more logical/secure way to see if data was acted and and we have the expected response
            $preEventData = $results->last();
        } else {
            $preEventData = $data;
        }

        // Hydrate entity with patched data
        $this->getHydrator()->hydrate((array) $preEventData, $entity);

        $results = $this->triggerDoctrineEvent(DoctrineResourceEvent::EVENT_PATCH_POST, $entity, $data);
        if ($results->last() instanceof ApiProblem) {
            return $results->last();
        }

        $this->getObjectManager()->flush();

        return $entity;
    }

    /**
     * Replace a collection or members of a collection
     *
     * @param mixed $data
     * @return ApiProblem|mixed
     */
    public function replaceList($data)
    {
        return new ApiProblem(405, 'The PUT method has not been defined for collections');
    }

    /**
     * Update a resource
     *
     * @param mixed $id
     * @param mixed $data
     * @return ApiProblem|mixed
     */
    public function update($id, $data)
    {
        $entity = $this->findEntity($id, 'update', $data);

        if ($entity instanceof ApiProblem) {
            return $entity;
        }

        $results = $this->triggerDoctrineEvent(DoctrineResourceEvent::EVENT_UPDATE_PRE, $entity, $data);
        if ($results->last() instanceof ApiProblem) {
            return $results->last();
        } elseif (! $results->isEmpty() && $results->last() !== null) {
            // TODO Change to a more logical/secure way to see if data was acted on and we have the expected response
            $preEventData = $results->last();
        } else {
            $preEventData = $data;
        }

        $this->getHydrator()->hydrate((array) $preEventData, $entity);

        $results = $this->triggerDoctrineEvent(DoctrineResourceEvent::EVENT_UPDATE_POST, $entity, $data);
        if ($results->last() instanceof ApiProblem) {
            return $results->last();
        }

        $this->getObjectManager()->flush();

        return $entity;
    }

    /**
     * This method will give custom listeners the chance to alter entities / collections.
     * Listeners can also return an ApiProblem, which will be returned immediately.
     * It is also possible to throw Exceptions, which will result in an ApiProblem eventually.
     *
     * @param string $name
     * @param object $entity
     * @param mixed $data The original data supplied to the resource method, if any
     * @return ResponseCollection
     */
    protected function triggerDoctrineEvent($name, $entity, $data = null)
    {
        $event = new DoctrineResourceEvent($name, $this);
        $event->setEntity($entity);
        $event->setData($data);
        $event->setObjectManager($this->getObjectManager());
        $event->setResourceEvent($this->getEvent());

        $eventManager = $this->getEventManager();
        return $eventManager->triggerEvent($event);
    }

    /**
     * Gets an entity by route params and/or the specified id
     *
     * @param string|int $id
     * @param string $method
     * @param null|array $data parameters
     * @return object
     */
    protected function findEntity($id, $method, $data = null)
    {
        // Match identity identifier name(s) with id(s)
        $ids      = explode($this->getMultiKeyDelimiter(), (string) $id);
        $keys     = explode($this->getMultiKeyDelimiter(), $this->getEntityIdentifierName());
        $criteria = [];

        if (count($ids) !== count($keys)) {
            return new ApiProblem(
                500,
                'Invalid multi identifier count.  '
                . count($ids)
                . ' must equal '
                . count($keys)
            );
        }

        foreach ($keys as $index => $identifier) {
            $criteria[$identifier] = $ids[$index];
        }

        $classMetaData       = $this->getObjectManager()->getClassMetadata($this->getEntityClass());
        $routeMatch          = $this->getEvent()->getRouteMatch();
        $associationMappings = $classMetaData->getAssociationNames();
        $fieldNames          = $classMetaData->getFieldNames();
        $routeParams         = $routeMatch->getParams();

        if (array_key_exists($this->getRouteIdentifierName(), $routeParams)) {
            unset($routeParams[$this->getRouteIdentifierName()]);
        }

        $reservedRouteParams = [
            'controller',
            'action',
            'version',
            ModuleRouteListener::MODULE_NAMESPACE,
            ModuleRouteListener::ORIGINAL_CONTROLLER,
        ];
        $allowedRouteParams  = array_diff_key($routeParams, array_flip($reservedRouteParams));

        /**
         * Append query selection parameters by route match.
         */
        foreach ($allowedRouteParams as $routeMatchParam => $value) {
            if (in_array($routeMatchParam, $associationMappings) || in_array($routeMatchParam, $fieldNames)) {
                $criteria[$routeMatchParam] = $value;
            }
        }

        // Build query
        $queryProvider = $this->getQueryProvider($method);
        $queryBuilder  = $queryProvider->createQuery($this->getEvent(), $this->getEntityClass(), $data);

        if ($queryBuilder instanceof ApiProblem) {
            return $queryBuilder;
        }

        // Add criteria
        foreach ($criteria as $key => $value) {
            if ($queryBuilder instanceof MongoDBQueryBuilder) {
                $queryBuilder->field($key)->equals($value);
            } else {
                $parameterName = 'a' . md5((string) rand());
                $queryBuilder->andwhere($queryBuilder->expr()->eq('row.' . $key, ":$parameterName"));
                $queryBuilder->setParameter($parameterName, $value, $classMetaData->getTypeOfField($key));
            }
        }

        try {
            $entity = $queryBuilder->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            $entity = null;
        }

        if (! $entity) {
            $entity = new ApiProblem(404, 'Entity was not found');
        }

        return $entity;
    }
}
