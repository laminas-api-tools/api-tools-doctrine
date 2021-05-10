<?php

namespace LaminasTest\ApiTools\Doctrine\Server\ORM\CRUD;

use Doctrine\Instantiator\InstantiatorInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Tools\SchemaTool;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\ApiProblem\ApiProblemResponse;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceEntity;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceResource;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRpcServiceEntity;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRpcServiceResource;
use Laminas\ApiTools\Doctrine\DoctrineResource;
use Laminas\ApiTools\Doctrine\Server\Event\DoctrineResourceEvent;
use Laminas\ApiTools\Rest\ResourceEvent;
use Laminas\Filter\FilterChain;
use Laminas\Http\Request;
use Laminas\ServiceManager\ServiceManager;
use LaminasTest\ApiTools\Doctrine\TestCase;
use LaminasTestApiToolsDb\Entity\Album;
use LaminasTestApiToolsDb\Entity\Artist;
use LaminasTestApiToolsDb\Entity\Product;
use LaminasTestApiToolsDbApi\V1\Rest\Artist\ArtistResource;
use LaminasTestApiToolsGeneral\Listener\EventCatcher;
use PHPUnit\Framework\Assert;

class CRUDTest extends TestCase
{
    /**
     * @var EntityManager
     */
    protected $em;

    protected function setUp()
    {
        parent::setUp();

        $this->setApplicationConfig(
            include __DIR__ . '/../../../../config/ORM/application.config.php'
        );

        $this->buildORMApi();
    }

    protected function buildORMApi()
    {
        $serviceManager = $this->getApplication()->getServiceManager();
        /** @var EntityManager $em */
        $em = $serviceManager->get('doctrine.entitymanager.orm_default');

        /** @var DoctrineRestServiceResource $restServiceResource */
        $restServiceResource = $serviceManager->get(DoctrineRestServiceResource::class);

        $artistResourceDefinition = [
            'objectManager'         => 'doctrine.entitymanager.orm_default',
            'serviceName'           => 'Artist',
            'entityClass'           => Artist::class,
            'routeIdentifierName'   => 'artist_id',
            'entityIdentifierName'  => 'id',
            'routeMatch'            => '/test/rest/artist',
            'collectionHttpMethods' => [
                0 => 'GET',
                1 => 'POST',
                2 => 'PATCH',
                3 => 'DELETE',
            ],
        ];

        $artistResourceDefinitionWithNonKeyIdentifier = [
            'objectManager'         => 'doctrine.entitymanager.orm_default',
            'serviceName'           => 'ArtistByName',
            'entityClass'           => Artist::class,
            'routeIdentifierName'   => 'artist_name',
            'entityIdentifierName'  => 'name',
            'routeMatch'            => '/test/rest/artist-by-name',
            'collectionHttpMethods' => [
                0 => 'GET',
            ],
        ];

        // This route is what should be an rpc service, but an user could do
        $albumResourceDefinition = [
            'objectManager'         => 'doctrine.entitymanager.orm_default',
            'serviceName'           => 'Album',
            'entityClass'           => Album::class,
            'routeIdentifierName'   => 'album_id',
            'entityIdentifierName'  => 'id',
            'routeMatch'            => '/test/rest[/artist/:artist_id]/album[/:album_id]',
            'collectionHttpMethods' => [
                0 => 'GET',
                1 => 'POST',
                2 => 'PATCH',
                3 => 'DELETE',
            ],
        ];

        $productResourceDefinition = [
            'objectManager'         => 'doctrine.entitymanager.orm_default',
            'serviceName'           => 'Product',
            'entityClass'           => Product::class,
            'routeIdentifierName'   => 'product_id',
            'entityIdentifierName'  => 'id',
            'routeMatch'            => '/test/rest/product',
            'collectionHttpMethods' => [
                0 => 'GET',
                1 => 'POST',
                2 => 'PATCH',
                3 => 'DELETE',
            ],
        ];

        $this->setModuleName($restServiceResource, 'LaminasTestApiToolsDbApi');
        $artistEntity       = $restServiceResource->create($artistResourceDefinition);
        $artistByNameEntity = $restServiceResource->create($artistResourceDefinitionWithNonKeyIdentifier);
        $albumEntity        = $restServiceResource->create($albumResourceDefinition);
        $productEntity      = $restServiceResource->create($productResourceDefinition);

        $this->assertInstanceOf(DoctrineRestServiceEntity::class, $artistEntity);
        $this->assertInstanceOf(DoctrineRestServiceEntity::class, $artistByNameEntity);
        $this->assertInstanceOf(DoctrineRestServiceEntity::class, $albumEntity);
        $this->assertInstanceOf(DoctrineRestServiceEntity::class, $productEntity);

        // Build relation
        $filter = new FilterChain();
        $filter->attachByName('WordCamelCaseToUnderscore')
            ->attachByName('StringToLower');

        $metadataFactory = $em->getMetadataFactory();
        $entityMetadata = $metadataFactory->getMetadataFor(Artist::class);

        /** @var DoctrineRpcServiceResource $rpcServiceResource */
        $rpcServiceResource = $serviceManager->get(DoctrineRpcServiceResource::class);
        $this->setModuleName($rpcServiceResource, 'LaminasTestApiToolsDbApi');

        foreach ($entityMetadata->associationMappings as $mapping) {
            switch ($mapping['type']) {
                case ClassMetadataInfo::ONE_TO_MANY:
                    $entity = $rpcServiceResource->create([
                        'service_name' => 'Artist' . $mapping['fieldName'],
                        'route_match' => '/test/artist[/:parent_id]/' . $filter($mapping['fieldName']) . '[/:child_id]',
                        'http_methods' => [
                            'GET',
                            'PUT',
                            'POST',
                        ],
                        'options' => [
                            'target_entity' => $mapping['targetEntity'],
                            'source_entity' => $mapping['sourceEntity'],
                            'field_name'    => $mapping['fieldName'],
                        ],
                        'selector' => 'custom selector',
                    ]);

                    $this->assertInstanceOf(DoctrineRpcServiceEntity::class, $entity);
                    break;
            }
        }

        $this->reset();

        $serviceManager = $this->getApplication()->getServiceManager();
        /** @var EntityManager $em */
        $em = $serviceManager->get('doctrine.entitymanager.orm_default');

        $tool = new SchemaTool($em);
        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());
        $this->em = $em;
    }

    public function testCreate()
    {
        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');

        $this->dispatch(
            '/test/rest/artist',
            Request::METHOD_POST,
            [
                'name' => 'ArtistOne',
                'createdAt' => '2016-08-09 22:30:42',
            ]
        );
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(201);
        $this->assertEquals('ArtistOne', $body['name']);
        $this->validateTriggeredEvents([
            DoctrineResourceEvent::EVENT_CREATE_PRE,
            DoctrineResourceEvent::EVENT_CREATE_POST,
        ]);
    }

    public function testCreateWithRelation()
    {
        $artist = $this->createArtist();
        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');

        $this->dispatch(
            '/test/rest/album',
            Request::METHOD_POST,
            [
                'name' => 'Album One',
                'createdAt' => '2016-08-21 22:32:38',
                'artist' => $artist->getId(),
            ]
        );
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(201);
        $this->assertEquals('Album One', $body['name']);
        $this->assertEquals($artist->getId(), $body['_embedded']['artist']['id']);
    }

    /**
     * @dataProvider listener
     *
     * @param string $method
     * @param string $message
     */
    public function testCreateWithListenerThatReturnsApiProblem($method, $message)
    {
        $this->$method(DoctrineResourceEvent::EVENT_CREATE_PRE);
        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');

        $this->dispatch(
            '/test/rest/artist',
            Request::METHOD_POST,
            [
                'name' => 'ArtistEleven',
                'createdAt' => '2016-08-21 22:33:17',
            ]
        );
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(400);
        $this->assertInstanceOf(ApiProblemResponse::class, $this->getResponse());
        $this->assertEquals(
            sprintf('%s: %s', $message, DoctrineResourceEvent::EVENT_CREATE_PRE),
            $body['detail']
        );
    }

    public function testFetchByCustomIdField()
    {
        $product = $this->createProduct();

        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');
        $this->getRequest()->setMethod(Request::METHOD_GET);

        $this->dispatch('/test/rest/product/' . $product->getId());
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(200);
        $this->assertEquals($product->getId(), $body['id']);
        $this->validateTriggeredEvents([
            DoctrineResourceEvent::EVENT_FETCH_PRE,
            DoctrineResourceEvent::EVENT_FETCH_POST,
        ]);
    }

    /**
     * @see https://github.com/zfcampus/zf-apigility-doctrine/pull/316
     */
    public function testFetchByCustomIdFieldIncludesApiToolsResourceEventInDoctrineResourceEvent()
    {
        $product = $this->createProduct();

        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');
        $this->getRequest()->setMethod(Request::METHOD_GET);

        $spy = (object) ['caught' => false];
        $sharedEvents = $this->getApplication()->getEventManager()->getSharedManager();
        $sharedEvents->attach(
            DoctrineResource::class,
            DoctrineResourceEvent::EVENT_FETCH_PRE,
            function (DoctrineResourceEvent $e) use ($spy) {
                Assert::assertInstanceOf(ResourceEvent::class, $e->getResourceEvent());
                $spy->caught = true;
            }
        );

        $this->dispatch('/test/rest/product/' . $product->getId());

        $this->assertResponseStatusCode(200);
        $this->assertTrue($spy->caught, 'EVENT_FETCH_PRE listener was not triggered');
    }

    public function testFetchEntityWithVersionFieldWithVersionParamInPath()
    {
        $product = $this->createProduct();

        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');
        $this->getRequest()->setMethod(Request::METHOD_GET);

        $this->dispatch('/v1/test/rest/product/' . $product->getId());
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(200);
        $this->assertEquals($product->getId(), $body['id']);
        $this->assertNull($body['version']);
        $this->validateTriggeredEvents([
            DoctrineResourceEvent::EVENT_FETCH_PRE,
            DoctrineResourceEvent::EVENT_FETCH_POST,
        ]);
    }

    public function testFetchByCustomIdFieldWithInvalidIdValue()
    {
        $product = $this->createProduct();

        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');
        $this->getRequest()->setMethod(Request::METHOD_GET);

        $this->dispatch('/test/rest/product/' . strrev($product->getId()));

        $this->assertResponseStatusCode(404);
        $this->validateTriggeredEvents([DoctrineResourceEvent::EVENT_FETCH_PRE]);
    }

    public function testCreateByExplicitlySettingEntityFactoryInConstructor()
    {
        /** @var InstantiatorInterface|\PHPUnit_Framework_MockObject_MockObject $entityFactoryMock */
        $entityFactoryMock = $this->getMockBuilder(InstantiatorInterface::class)->getMock();
        $entityFactoryMock->expects(self::once())
            ->method('instantiate')
            ->with(Artist::class)
            ->willReturnCallback(function ($class) {
                return new $class();
            });

        /** @var ServiceManager $sm */
        $sm = $this->getApplication()->getServiceManager();

        $config = $sm->get('config');
        $resourceName = 'LaminasTestApiToolsDbApi\V1\Rest\Artist\ArtistResource';
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
            '/test/rest/artist',
            Request::METHOD_POST,
            [
                'name' => 'ArtistTwelve',
                'createdAt' => '2017-03-01 08:56:32',
            ]
        );

        $this->assertResponseStatusCode(201);
    }

    public function testFetch()
    {
        $artist = $this->createArtist('Artist Name');
        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');
        $this->getRequest()->setMethod(Request::METHOD_GET);

        $this->dispatch('/test/rest/artist/' . $artist->getId());
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(200);
        $this->assertEquals('Artist Name', $body['name']);
        $this->validateTriggeredEvents([
            DoctrineResourceEvent::EVENT_FETCH_PRE,
            DoctrineResourceEvent::EVENT_FETCH_POST,
        ]);
    }

    public function testFetchWithNonPrimaryKeyIdentifier()
    {
        $artist = $this->createArtist('ArtistTwo');
        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');
        $this->getRequest()->setMethod(Request::METHOD_GET);

        $this->dispatch('/test/rest/artist-by-name/' . $artist->getName());
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(200);
        $this->assertEquals('ArtistTwo', $body['name']);
    }

    public function testFetchWithRelation()
    {
        $artist = $this->createArtist('NewArtist');
        $album = $this->createAlbum('NewAlbum', $artist);
        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');
        $this->getRequest()->setMethod(Request::METHOD_GET);

        $this->dispatch('/test/rest/artist/' . $artist->getId() . '/album/' . $album->getId());
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(200);
        $this->assertEquals('NewAlbum', $body['name']);
    }

    /**
     * @dataProvider listener
     *
     * @param string $method
     * @param string $message
     */
    public function testFetchWithListenerThatReturnsApiProblem($method, $message)
    {
        $artist = $this->createArtist('Artist Fetch ApiProblem');
        $this->$method(DoctrineResourceEvent::EVENT_FETCH_PRE);
        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');

        $this->dispatch('/test/rest/artist/' . $artist->getId());
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(400);
        $this->assertInstanceOf(ApiProblemResponse::class, $this->getResponse());
        $this->assertEquals(
            sprintf('%s: %s', $message, DoctrineResourceEvent::EVENT_FETCH_PRE),
            $body['detail']
        );
    }

    public function testFetchAll()
    {
        $artist1 = $this->createArtist('Artist 1');
        $artist2 = $this->createArtist('Artist 2');
        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');
        $this->getRequest()->setMethod(Request::METHOD_GET);

        $this->dispatch('/test/rest/artist');
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(200);
        $this->assertEquals(2, $body['total_items']);
        $this->assertCount(2, $body['_embedded']['artist']);
        $this->assertEquals($artist1->getId(), $body['_embedded']['artist'][0]['id']);
        $this->assertEquals($artist2->getId(), $body['_embedded']['artist'][1]['id']);
        $this->validateTriggeredEvents([
            DoctrineResourceEvent::EVENT_FETCH_ALL_PRE,
            DoctrineResourceEvent::EVENT_FETCH_ALL_POST,
        ]);
    }

    public function testFetchAllEmptyCollection()
    {
        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');
        $this->getRequest()->setMethod(Request::METHOD_GET);

        $this->dispatch('/test/rest/artist');
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(200);
        $this->assertEquals(0, $body['total_items']);
        $this->assertCount(0, $body['_embedded']['artist']);
        $this->validateTriggeredEvents([
            DoctrineResourceEvent::EVENT_FETCH_ALL_PRE,
            DoctrineResourceEvent::EVENT_FETCH_ALL_POST,
        ]);
    }

    /**
     * @dataProvider listener
     *
     * @param string $method
     * @param string $message
     */
    public function testFetchAllWithListenerThatReturnsApiProblem($method, $message)
    {
        $this->createArtist('Artist FetchAll ApiProblem');
        $this->$method(DoctrineResourceEvent::EVENT_FETCH_ALL_PRE);
        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');

        $this->dispatch('/test/rest/artist');
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(400);
        $this->assertInstanceOf(ApiProblemResponse::class, $this->getResponse());
        $this->assertEquals(
            sprintf('%s: %s', $message, DoctrineResourceEvent::EVENT_FETCH_ALL_PRE),
            $body['detail']
        );
    }

    public function testPatch()
    {
        $artist = $this->createArtist('Artist Patch');
        $this->getRequest()->getHeaders()->addHeaders([
            'Accept' => 'application/json',
            'Content-type' => 'application/json',
        ]);
        $this->getRequest()->setMethod(Request::METHOD_PATCH);
        $this->getRequest()->setContent(json_encode(['name' => 'Artist Patch Edit']));

        $this->dispatch('/test/rest/artist/' . $artist->getId());
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(200);
        $this->assertEquals('Artist Patch Edit', $body['name']);
        $this->assertEquals($artist->getId(), $body['id']);
        $foundEntity = $this->em->getRepository(Artist::class)->find($artist->getId());
        $this->assertEquals('Artist Patch Edit', $foundEntity->getName());
        $this->validateTriggeredEvents([
            DoctrineResourceEvent::EVENT_PATCH_PRE,
            DoctrineResourceEvent::EVENT_PATCH_POST,
        ]);
    }

    /**
     * @dataProvider listener
     *
     * @param string $method
     * @param string $message
     */
    public function testPatchWithListenerThatReturnsApiProblem($method, $message)
    {
        $artist = $this->createArtist('Artist Patch ApiProblem');
        $this->$method(DoctrineResourceEvent::EVENT_PATCH_PRE);
        $this->getRequest()->getHeaders()->addHeaders([
            'Accept' => 'application/json',
            'Content-type' => 'application/json',
        ]);
        $this->getRequest()->setMethod(Request::METHOD_PATCH);
        $this->getRequest()->setContent(json_encode(['name' => 'ArtistTenPatchEdit']));

        $this->dispatch('/test/rest/artist/' . $artist->getId());
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(400);
        $this->assertInstanceOf(ApiProblemResponse::class, $this->getResponse());
        $this->assertEquals(
            sprintf('%s: %s', $message, DoctrineResourceEvent::EVENT_PATCH_PRE),
            $body['detail']
        );
    }

    public function testPatchList()
    {
        $artist1 = $this->createArtist('Artist Patch List 1');
        $artist2 = $this->createArtist('Artist Patch List 2');
        $artist3 = $this->createArtist('Artist Patch List 3');

        $patchList = [
            [
                'id' => $artist1->getId(),
                'name' => 'oneNewName',
            ],
            [
                'id' => $artist2->getId(),
                'name' => 'twoNewName',
            ],
            [
                'id' => $artist3->getId(),
                'name' => 'threeNewName',
            ],
        ];

        $this->em->clear();

        $this->getRequest()->getHeaders()->addHeaders([
            'Accept' => 'application/json',
            'Content-type' => 'application/json',
        ]);
        $this->getRequest()->setMethod(Request::METHOD_PATCH);
        $this->getRequest()->setContent(json_encode($patchList));

        $this->dispatch('/test/rest/artist');
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(200);
        $this->assertEquals('oneNewName', $body['_embedded']['artist'][0]['name']);
        $this->assertEquals('twoNewName', $body['_embedded']['artist'][1]['name']);
        $this->assertEquals('threeNewName', $body['_embedded']['artist'][2]['name']);
        $this->validateTriggeredEventsContains([
            DoctrineResourceEvent::EVENT_PATCH_LIST_PRE,
            DoctrineResourceEvent::EVENT_PATCH_LIST_POST,
        ]);
    }

    /**
     * @dataProvider listener
     *
     * @param string $method
     * @param string $message
     */
    public function testPatchListWithListenerThatReturnsApiProblem($method, $message)
    {
        $artist = $this->createArtist('Artist Patch List ApiProblem');
        $this->$method(DoctrineResourceEvent::EVENT_PATCH_LIST_PRE);
        $this->getRequest()->getHeaders()->addHeaders([
            'Accept' => 'application/json',
            'Content-type' => 'application/json',
        ]);
        $this->getRequest()->setMethod(Request::METHOD_PATCH);
        $this->getRequest()->setContent(json_encode([
            [
                'id' => $artist->getId(),
                'name' => 'Artist Edit',
            ],
        ]));

        $this->dispatch('/test/rest/artist');
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(400);
        $this->assertInstanceOf(ApiProblemResponse::class, $this->getResponse());
        $this->assertEquals(
            sprintf('%s: %s', $message, DoctrineResourceEvent::EVENT_PATCH_LIST_PRE),
            $body['detail']
        );
    }

    public function testPut()
    {
        $artist = $this->createArtist('Artist Put');
        $this->getRequest()->getHeaders()->addHeaders([
            'Accept' => 'application/json',
            'Content-type' => 'application/json',
        ]);
        $this->getRequest()->setMethod(Request::METHOD_PUT);
        $this->getRequest()->setContent(json_encode([
            'name' => 'Artist Put Edit',
            'createdAt' => '2016-08-21 22:10:11',
        ]));

        $this->dispatch('/test/rest/artist/' . $artist->getId());
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(200);
        $this->assertEquals('Artist Put Edit', $body['name']);
        $foundEntity = $this->em->getRepository(Artist::class)->find($artist->getId());
        $this->assertEquals('Artist Put Edit', $foundEntity->getName());
        $this->assertEquals('2016-08-21 22:10:11', $foundEntity->getCreatedAt()->format('Y-m-d H:i:s'));
        $this->validateTriggeredEvents([
            DoctrineResourceEvent::EVENT_UPDATE_PRE,
            DoctrineResourceEvent::EVENT_UPDATE_POST,
        ]);
    }

    /**
     * @dataProvider listener
     *
     * @param string $method
     * @param string $message
     */
    public function testPutWithListenerThatReturnsApiProblem($method, $message)
    {
        $artist = $this->createArtist('Artist Put ApiProblem');
        $this->$method(DoctrineResourceEvent::EVENT_UPDATE_PRE);
        $this->getRequest()->getHeaders()->addHeaders([
            'Accept' => 'application/json',
            'Content-type' => 'application/json',
        ]);
        $this->getRequest()->setMethod(Request::METHOD_PUT);
        $this->getRequest()->setContent(json_encode([
            'name' => 'Artist Put Edit',
            'createdAt' => '2016-08-21 22:10:19',
        ]));

        $this->dispatch('/test/rest/artist/' . $artist->getId());
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(400);
        $this->assertInstanceOf(ApiProblemResponse::class, $this->getResponse());
        $this->assertEquals(
            sprintf('%s: %s', $message, DoctrineResourceEvent::EVENT_UPDATE_PRE),
            $body['detail']
        );
    }

    public function testDelete()
    {
        $artist = $this->createArtist('Artist Delete');
        $id = $artist->getId();
        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');
        $this->getRequest()->setMethod(Request::METHOD_DELETE);

        $this->dispatch('/test/rest/artist/' . $id);

        $this->assertResponseStatusCode(204);
        $this->assertNull($this->em->getRepository(Artist::class)->find($id));
        $this->validateTriggeredEvents([
            DoctrineResourceEvent::EVENT_DELETE_PRE,
            DoctrineResourceEvent::EVENT_DELETE_POST,
        ]);
    }

    /**
     * @dataProvider listener
     *
     * @param string $method
     * @param string $message
     */
    public function testDeleteWithListenerThatReturnsApiProblem($method, $message)
    {
        $artist = $this->createArtist('Artist Delete ApiProblem');
        $this->$method(DoctrineResourceEvent::EVENT_DELETE_PRE);
        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');
        $this->getRequest()->setMethod(Request::METHOD_DELETE);

        $this->dispatch('/test/rest/artist/' . $artist->getId());
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(400);
        $this->assertInstanceOf(ApiProblemResponse::class, $this->getResponse());
        $this->assertEquals(
            sprintf('%s: %s', $message, DoctrineResourceEvent::EVENT_DELETE_PRE),
            $body['detail']
        );
        $foundEntity = $this->em->getRepository(Artist::class)->find($artist->getId());
        $this->assertEquals($artist->getId(), $foundEntity->getId());
    }

    public function testDeleteEntityNotFound()
    {
        $artist = $this->createArtist();
        $id = $artist->getId() + 1;
        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');
        $this->getRequest()->setMethod(Request::METHOD_DELETE);

        $this->dispatch('/test/rest/artist/' . $id);

        $this->assertResponseStatusCode(404);
        $this->validateTriggeredEvents([]);
        $this->assertNull($this->em->getRepository(Artist::class)->find($id));
    }

    public function testDeleteEntityDeleted()
    {
        $artist = $this->createArtist();
        $id = $artist->getId();
        $this->em->remove($artist);
        $this->em->flush();
        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');
        $this->getRequest()->setMethod(Request::METHOD_DELETE);

        $this->dispatch('/test/rest/artist/' . $id);

        $this->assertResponseStatusCode(404);
        $this->validateTriggeredEvents([]);
        $this->assertNull($this->em->getRepository(Artist::class)->find($id));
    }

    public function testDeleteList()
    {
        $artist1 = $this->createArtist('Artist Delete 1');
        $artist2 = $this->createArtist('Artist Delete 2');
        $artist3 = $this->createArtist('Artist Delete 3');

        $deleteList = [
            ['id' => $artist1->getId()],
            ['id' => $artist2->getId()],
            ['id' => $artist3->getId()],
        ];

        $this->em->clear();

        $this->getRequest()->getHeaders()->addHeaders([
            'Accept' => 'application/json',
            'Content-type' => 'application/json',
        ]);
        $this->getRequest()->setMethod(Request::METHOD_DELETE);
        $this->getRequest()->setContent(json_encode($deleteList));

        $this->dispatch('/test/rest/artist');

        $this->assertResponseStatusCode(204);
        $this->assertNull($this->em->getRepository(Artist::class)->find($artist1->getId()));
        $this->assertNull($this->em->getRepository(Artist::class)->find($artist2->getId()));
        $this->assertNull($this->em->getRepository(Artist::class)->find($artist3->getId()));
        $this->validateTriggeredEventsContains([
            DoctrineResourceEvent::EVENT_DELETE_LIST_PRE,
            DoctrineResourceEvent::EVENT_DELETE_LIST_POST,
        ]);
    }

    /**
     * @dataProvider listener
     *
     * @param string $method
     * @param string $message
     */
    public function testDeleteListWithListenerThatReturnsApiProblem($method, $message)
    {
        $this->$method(DoctrineResourceEvent::EVENT_DELETE_LIST_PRE);

        $artist1 = $this->createArtist('Artist Delete 1');
        $artist2 = $this->createArtist('Artist Delete 2');
        $artist3 = $this->createArtist('Artist Delete 3');

        $deleteList = [
            ['id' => $artist1->getId()],
            ['id' => $artist2->getId()],
            ['id' => $artist3->getId()],
        ];

        $this->em->clear();

        $this->getRequest()->getHeaders()->addHeaders([
            'Accept' => 'application/json',
            'Content-type' => 'application/json',
        ]);
        $this->getRequest()->setMethod(Request::METHOD_DELETE);
        $this->getRequest()->setContent(json_encode($deleteList));

        $this->dispatch('/test/rest/artist');
        $body = json_decode($this->getResponse()->getBody(), true);

        $this->assertResponseStatusCode(400);
        $this->assertInstanceOf(ApiProblemResponse::class, $this->getResponse());
        $this->assertEquals(
            sprintf('%s: %s', $message, DoctrineResourceEvent::EVENT_DELETE_LIST_PRE),
            $body['detail']
        );
        $foundEntity1 = $this->em->getRepository(Artist::class)->find($artist1->getId());
        $foundEntity2 = $this->em->getRepository(Artist::class)->find($artist2->getId());
        $foundEntity3 = $this->em->getRepository(Artist::class)->find($artist3->getId());
        $this->assertEquals($artist1->getId(), $foundEntity1->getId());
        $this->assertEquals($artist2->getId(), $foundEntity2->getId());
        $this->assertEquals($artist3->getId(), $foundEntity3->getId());
    }

    public function testGetRpcNoParams()
    {
        $this->markTestIncomplete('Doctrine RPC Services are not fully implemented.');

        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');

        $this->dispatch('/test/artist/album');
        $body = json_decode($this->getResponse()->getBody(), true);

        print_r($body);
    }

    public function testGetRpcWithParams()
    {
        $this->markTestIncomplete('Doctrine RPC Services are not fully implemented.');

        $artist = $this->createArtist('Artist RPC');
        $album = $this->createAlbum('Album RPC', $artist);

        $this->getRequest()->getHeaders()->addHeaderLine('Accept', 'application/json');

        $this->dispatch(sprintf('/test/artist/%d/album/%d', $artist->getId(), $album->getId()));
        $body = json_decode($this->getResponse()->getBody(), true);

        print_r($body);
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
     * @param array $expectedEvents
     */
    protected function validateTriggeredEventsContains(array $expectedEvents)
    {
        $serviceManager = $this->getApplication()->getServiceManager();
        $eventCatcher = $serviceManager->get(EventCatcher::class);

        foreach ($expectedEvents as $event) {
            $this->assertTrue(
                in_array($event, $eventCatcher->getCaughtEvents()),
                sprintf(
                    'Did not identify event "%s" in caught events',
                    $event
                )
            );
        }
    }

    /**
     * @param null|string $name
     * @return Artist
     */
    protected function createArtist($name = null)
    {
        $artist = new Artist();
        $artist->setName($name ?: 'Artist name');
        $artist->setCreatedAt(new \DateTime());
        $this->em->persist($artist);
        $this->em->flush();

        return $artist;
    }

    /**
     * @param null|string $name
     * @param null|Artist $artist
     * @return Album
     */
    protected function createAlbum($name = null, Artist $artist = null)
    {
        $album = new Album();
        $album->setName($name ?: 'Album name');
        $album->setArtist($artist ?: $this->createArtist());
        $album->setCreatedAt(new \DateTime());
        $this->em->persist($album);
        $this->em->flush();

        return $album;
    }

    /**
     * @return Product
     */
    protected function createProduct()
    {
        $product = new Product();
        $this->em->persist($product);
        $this->em->flush();

        return $product;
    }

    public function listener()
    {
        return [
            //          $methodToAttachListener,     $detailMessage
            'shared' => ['attachSharedListener',     'LaminasTestSharedListenerFailure'],
            'config' => ['attachAggregatedListener', 'LaminasTestFailureAggregateListener'],
        ];
    }

    /**
     * @param string $eventName
     * @return void
     */
    protected function attachSharedListener($eventName)
    {
        $sharedEvents = $this->getApplication()->getEventManager()->getSharedManager();
        $sharedEvents->attach(
            DoctrineResource::class,
            $eventName,
            function (DoctrineResourceEvent $e) use ($eventName) {
                $e->stopPropagation();
                return new ApiProblem(400, sprintf('LaminasTestSharedListenerFailure: %s', $eventName));
            }
        );
    }

    /**
     * @param string $eventName
     * @return void
     */
    protected function attachAggregatedListener($eventName)
    {
        $sm = $this->getApplication()->getServiceManager();
        $sm->setAllowOverride(true);
        $config = $sm->get('config');
        $config['api-tools']['doctrine-connected'][ArtistResource::class]['listeners'][]
            = 'LaminasTestFailureAggregateListener';
        $sm->setService('config', $config);
        $sm->setAllowOverride(false);

        $listener = new TestAsset\FailureAggregateListener($eventName);
        $sm->setService('LaminasTestFailureAggregateListener', $listener);
    }
}
