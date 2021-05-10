<?php

namespace LaminasTest\ApiTools\Doctrine\Server\ODM\CRUD;

use Doctrine\Instantiator\InstantiatorInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\ApiProblem\ApiProblemResponse;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceEntity;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceResource;
use Laminas\ApiTools\Doctrine\DoctrineResource;
use Laminas\ApiTools\Doctrine\Server\Event\DoctrineResourceEvent;
use Laminas\Http\Request;
use Laminas\ServiceManager\ServiceManager;
use LaminasTest\ApiTools\Doctrine\TestCase;
use LaminasTestApiToolsDbMongo\Document\Meta;
use LaminasTestApiToolsGeneral\Listener\EventCatcher;
use MongoClient;

class CRUDTest extends TestCase
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    protected function setUp()
    {
        parent::setUp();

        $this->setApplicationConfig(
            include __DIR__ . '/../../../../config/ODM/application.config.php'
        );

        $this->buildODMApi();
    }

    protected function tearDown()
    {
        $this->clearData();

        parent::tearDown();
    }

    protected function buildODMApi()
    {
        $serviceManager = $this->getApplication()->getServiceManager();

        /** @var DoctrineRestServiceResource $resource */
        $resource = $serviceManager->get(DoctrineRestServiceResource::class);

        $metaResourceDefinition = [
            'objectManager'        => 'doctrine.documentmanager.odm_default',
            'serviceName'          => 'Meta',
            'entityClass'          => Meta::class,
            'routeIdentifierName'  => 'meta_id',
            'entityIdentifierName' => 'id',
            'routeMatch'           => '/test/meta',
        ];

        $this->setModuleName($resource, 'LaminasTestApiToolsDbMongoApi');
        $metaEntity = $resource->create($metaResourceDefinition);

        $this->assertInstanceOf(DoctrineRestServiceEntity::class, $metaEntity);

        $this->reset();

        $serviceManager = $this->getApplication()->getServiceManager();
        $this->dm = $serviceManager->get('doctrine.documentmanager.odm_default');
    }

    protected function clearData()
    {
        $config = $this->getApplication()->getConfig();
        $config = $config['doctrine']['connection']['odm_default'];

        $connection = new MongoClient('mongodb://' . $config['server'] . ':' . $config['port']);
        $db = $connection->{$config['dbname']};
        $collection = $db->meta;
        $collection->remove();
    }

    public function testCreate()
    {
        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');

        $this->dispatch(
            '/test/meta',
            Request::METHOD_POST,
            [
                'name' => 'MetaOne',
                'createdAt' => '2016-08-21 23:04:19',
            ]
        );
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(201);
        $this->assertEquals('MetaOne', $body['name']);
        $this->validateTriggeredEvents([
            DoctrineResourceEvent::EVENT_CREATE_PRE,
            DoctrineResourceEvent::EVENT_CREATE_POST,
        ]);
    }

    public function testCreateWithListenerThatReturnsApiProblem()
    {
        $sharedEvents = $this->getApplication()->getEventManager()->getSharedManager();
        $sharedEvents->attach(
            DoctrineResource::class,
            DoctrineResourceEvent::EVENT_CREATE_PRE,
            function (DoctrineResourceEvent $e) {
                $e->stopPropagation();
                return new ApiProblem(400, 'LaminasTestCreateFailure');
            }
        );
        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');

        $this->dispatch(
            '/test/meta',
            Request::METHOD_POST,
            ['name' => 'Meta ODM', 'createdAt' => '2016-08-21 23:09:58']
        );
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(400);
        $this->assertInstanceOf(ApiProblemResponse::class, $this->getResponse());
        $this->assertEquals('LaminasTestCreateFailure', $body['detail']);
    }

    public function testCreateByExplicitlySettingEntityFactoryInConstructor()
    {
        /** @var InstantiatorInterface|\PHPUnit_Framework_MockObject_MockObject $entityFactoryMock */
        $entityFactoryMock = $this->getMockBuilder(InstantiatorInterface::class)->getMock();
        $entityFactoryMock->expects(self::once())
            ->method('instantiate')
            ->with(Meta::class)
            ->willReturnCallback(function ($class) {
                return new $class();
            });

        /** @var ServiceManager $sm */
        $sm = $this->getApplication()->getServiceManager();

        $config = $sm->get('config');
        $resourceName = 'LaminasTestApiToolsDbMongoApi\V1\Rest\Meta\MetaResource';
        $resourceConfig = $config['api-tools']['doctrine-connected'][$resourceName];
        $resourceConfig['entity_factory'] = 'ResourceInstantiator';
        $config['api-tools']['doctrine-connected'][$resourceName] = $resourceConfig;

        $sm->setAllowOverride(true);
        $sm->setService('config', $config);
        $sm->setAllowOverride(false);

        $sm->setService(
            'ResourceInstantiator',
            $entityFactoryMock
        );

        // dispatch a request to create a meta document (similar to testCreate())
        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');

        $this->dispatch(
            '/test/meta',
            Request::METHOD_POST,
            [
                'name' => 'MetaOne',
                'createdAt' => '2016-08-21 23:04:19',
            ]
        );
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(201);
        $this->assertEquals('MetaOne', $body['name']);
        $this->validateTriggeredEvents([
            DoctrineResourceEvent::EVENT_CREATE_PRE,
            DoctrineResourceEvent::EVENT_CREATE_POST,
        ]);
    }

    public function testFetch()
    {
        $meta = $this->createMeta('Meta Fetch');
        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');
        $this->getRequest()->setMethod(Request::METHOD_GET);

        $this->dispatch('/test/meta/' . $meta->getId());
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(200);
        $this->assertEquals('Meta Fetch', $body['name']);
        $this->validateTriggeredEvents([
            DoctrineResourceEvent::EVENT_FETCH_PRE,
            DoctrineResourceEvent::EVENT_FETCH_POST,
        ]);
    }

    public function testFetchWithListenerThatReturnsApiProblem()
    {
        $meta = $this->createMeta('Meta Fetch ApiProblem');
        $sharedEvents = $this->getApplication()->getEventManager()->getSharedManager();
        $sharedEvents->attach(
            DoctrineResource::class,
            DoctrineResourceEvent::EVENT_FETCH_PRE,
            function (DoctrineResourceEvent $e) {
                $e->stopPropagation();
                return new ApiProblem(400, 'LaminasTestFetchFailure');
            }
        );

        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');

        $this->dispatch('/test/meta/' . $meta->getId());
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(400);
        $this->assertInstanceOf(ApiProblemResponse::class, $this->getResponse());
        $this->assertEquals('LaminasTestFetchFailure', $body['detail']);
    }

    public function testFetchAll()
    {
        $meta1 = $this->createMeta('Meta 1');
        $meta2 = $this->createMeta('Meta 2');
        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');
        $this->getRequest()->setMethod(Request::METHOD_GET);

        $this->dispatch('/test/meta');
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(200);
        $this->assertEquals(2, $body['total_items']);
        $this->assertCount(2, $body['_embedded']['meta']);
        $this->assertEquals($meta1->getId(), $body['_embedded']['meta'][0]['id']);
        $this->assertEquals($meta2->getId(), $body['_embedded']['meta'][1]['id']);
        $this->validateTriggeredEvents([
            DoctrineResourceEvent::EVENT_FETCH_ALL_PRE,
            DoctrineResourceEvent::EVENT_FETCH_ALL_POST,
        ]);
    }

    public function testFetchAllEmptyCollection()
    {
        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');
        $this->getRequest()->setMethod(Request::METHOD_GET);

        $this->dispatch('/test/meta');
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(200);
        $this->assertEquals(0, $body['total_items']);
        $this->assertCount(0, $body['_embedded']['meta']);
        $this->validateTriggeredEvents([
            DoctrineResourceEvent::EVENT_FETCH_ALL_PRE,
            DoctrineResourceEvent::EVENT_FETCH_ALL_POST,
        ]);
    }

    public function testFetchAllWithListenerThatReturnsApiProblem()
    {
        $this->createMeta('Meta FetchAll ApiProblem');
        $sharedEvents = $this->getApplication()->getEventManager()->getSharedManager();
        $sharedEvents->attach(
            DoctrineResource::class,
            DoctrineResourceEvent::EVENT_FETCH_ALL_PRE,
            function (DoctrineResourceEvent $e) {
                $e->stopPropagation();
                return new ApiProblem(400, 'LaminasTestFetchAllFailure');
            }
        );
        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');

        $this->dispatch('/test/meta');
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(400);
        $this->assertInstanceOf(ApiProblemResponse::class, $this->getResponse());
        $this->assertEquals('LaminasTestFetchAllFailure', $body['detail']);
    }

    public function testPatch()
    {
        $meta = $this->createMeta('Meta Patch');
        $this->getRequest()->getHeaders()->addHeaders([
            'Accept' => 'application/json',
            'Content-type' => 'application/json',
        ]);
        $this->getRequest()->setMethod(Request::METHOD_PATCH);
        $this->getRequest()->setContent(json_encode(['name' => 'Meta Patch Edit']));

        $this->dispatch('/test/meta/' . $meta->getId());
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(200);
        $this->assertEquals('Meta Patch Edit', $body['name']);
        $this->assertEquals($meta->getId(), $body['id']);
        $foundEntity = $this->dm->getRepository(Meta::class)->find($meta->getId());
        $this->assertEquals('Meta Patch Edit', $foundEntity->getName());
        $this->validateTriggeredEvents([
            DoctrineResourceEvent::EVENT_PATCH_PRE,
            DoctrineResourceEvent::EVENT_PATCH_POST,
        ]);
    }

    public function testPatchWithListenerThatReturnsApiProblem()
    {
        $meta = $this->createMeta('Meta Patch ApiProblem');
        $sharedEvents = $this->getApplication()->getEventManager()->getSharedManager();
        $sharedEvents->attach(
            DoctrineResource::class,
            DoctrineResourceEvent::EVENT_PATCH_PRE,
            function (DoctrineResourceEvent $e) {
                $e->stopPropagation();
                return new ApiProblem(400, 'LaminasTestPatchFailure');
            }
        );
        $this->getRequest()->getHeaders()->addHeaders([
            'Accept' => 'application/json',
            'Content-type' => 'application/json',
        ]);
        $this->getRequest()->setMethod(Request::METHOD_PATCH);
        $this->getRequest()->setContent(json_encode(['name' => 'MetaTenPatchEdit']));

        $this->dispatch('/test/meta/' . $meta->getId());
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(400);
        $this->assertInstanceOf(ApiProblemResponse::class, $this->getResponse());
        $this->assertEquals('LaminasTestPatchFailure', $body['detail']);
    }

    public function testPut()
    {
        $meta = $this->createMeta('Meta Put');
        $this->getRequest()->getHeaders()->addHeaders([
            'Accept' => 'application/json',
            'Content-type' => 'application/json',
        ]);
        $this->getRequest()->setMethod(Request::METHOD_PUT);
        $this->getRequest()->setContent(json_encode([
            'name' => 'Meta Put Edit',
            'createdAt' => '2016-08-22 00:08:19',
        ]));

        $this->dispatch('/test/meta/' . $meta->getId());
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(200);
        $this->assertEquals('Meta Put Edit', $body['name']);
        $foundEntity = $this->dm->getRepository(Meta::class)->find($meta->getId());
        $this->assertEquals('Meta Put Edit', $foundEntity->getName());
        $this->assertEquals('2016-08-22 00:08:19', $foundEntity->getCreatedAt()->format('Y-m-d H:i:s'));
        $this->validateTriggeredEvents([
            DoctrineResourceEvent::EVENT_UPDATE_PRE,
            DoctrineResourceEvent::EVENT_UPDATE_POST,
        ]);
    }

    public function testPutWithListenerThatReturnsApiProblem()
    {
        $meta = $this->createMeta('Meta Put ApiProblem');
        $sharedEvents = $this->getApplication()->getEventManager()->getSharedManager();
        $sharedEvents->attach(
            DoctrineResource::class,
            DoctrineResourceEvent::EVENT_UPDATE_PRE,
            function (DoctrineResourceEvent $e) {
                $e->stopPropagation();
                return new ApiProblem(400, 'LaminasTestPutFailure');
            }
        );
        $this->getRequest()->getHeaders()->addHeaders([
            'Accept' => 'application/json',
            'Content-type' => 'application/json',
        ]);
        $this->getRequest()->setMethod(Request::METHOD_PUT);
        $this->getRequest()->setContent(json_encode([
            'name' => 'Meta Put Edit',
            'createdAt' => '2016-08-21 22:10:19',
        ]));

        $this->dispatch('/test/meta/' . $meta->getId());
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(400);
        $this->assertInstanceOf(ApiProblemResponse::class, $this->getResponse());
        $this->assertEquals('LaminasTestPutFailure', $body['detail']);
    }

    public function testDelete()
    {
        $meta = $this->createMeta('Meta Delete');
        $id = $meta->getId();
        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');
        $this->getRequest()->setMethod(Request::METHOD_DELETE);

        $this->dispatch('/test/meta/' . $id);

        $this->assertResponseStatusCode(204);
        $this->assertNull($this->dm->getRepository(Meta::class)->find($id));
        $this->validateTriggeredEvents([
            DoctrineResourceEvent::EVENT_DELETE_PRE,
            DoctrineResourceEvent::EVENT_DELETE_POST,
        ]);
    }

    public function testDeleteWithListenerThatReturnsApiProblem()
    {
        $meta = $this->createMeta('Meta Delete ApiProblem');
        $sharedEvents = $this->getApplication()->getEventManager()->getSharedManager();
        $sharedEvents->attach(
            DoctrineResource::class,
            DoctrineResourceEvent::EVENT_DELETE_PRE,
            function (DoctrineResourceEvent $e) {
                $e->stopPropagation();
                return new ApiProblem(400, 'LaminasTestDeleteFailure');
            }
        );
        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');
        $this->getRequest()->setMethod(Request::METHOD_DELETE);

        $this->dispatch('/test/meta/' . $meta->getId());
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(400);
        $this->assertInstanceOf(ApiProblemResponse::class, $this->getResponse());
        $this->assertEquals('LaminasTestDeleteFailure', $body['detail']);
        $foundEntity = $this->dm->getRepository(Meta::class)->find($meta->getId());
        $this->assertEquals($meta->getId(), $foundEntity->getId());
    }

    public function testDeleteEntityNotFound()
    {
        $meta = $this->createMeta();
        $id = $meta->getId() . '0';
        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');
        $this->getRequest()->setMethod(Request::METHOD_DELETE);

        $this->dispatch('/test/meta/' . $id);

        $this->assertResponseStatusCode(404);
        $this->validateTriggeredEvents([]);
        $this->assertNull($this->dm->getRepository(Meta::class)->find($id));
    }

    public function testDeleteEntityDeleted()
    {
        $meta = $this->createMeta();
        $id = $meta->getId();
        $this->dm->remove($meta);
        $this->dm->flush();
        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');
        $this->getRequest()->setMethod(Request::METHOD_DELETE);

        $this->dispatch('/test/meta/' . $id);

        $this->assertResponseStatusCode(404);
        $this->validateTriggeredEvents([]);
        $this->assertNull($this->dm->getRepository(Meta::class)->find($id));
    }

    /**
     * @param array $expectedEvents
     */
    protected function validateTriggeredEvents(array $expectedEvents)
    {
        $serviceManager = $this->getApplication()->getServiceManager();
        $eventCatcher = $serviceManager->get(EventCatcher::class);

        $this->assertEquals($expectedEvents, $eventCatcher->getCaughtEvents());
    }

    /**
     * @param null|string $name
     * @return Meta
     */
    protected function createMeta($name = null)
    {
        $meta = new Meta();
        $meta->setName($name ?: 'Meta Name');
        $meta->setCreatedAt(new \DateTime());
        $this->dm->persist($meta);
        $this->dm->flush();

        return $meta;
    }
}
