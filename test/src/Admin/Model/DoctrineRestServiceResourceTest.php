<?php

declare(strict_types=1);

namespace LaminasTest\ApiTools\Doctrine\Admin\Model;

use DateTime;
use LaminasTestApiToolsDb\Entity\Artist;
use Doctrine\ORM\Tools\SchemaTool;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceEntity;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceResource;
use LaminasTest\ApiTools\Doctrine\TestCase;

class DoctrineRestServiceResourceTest extends TestCase
{
    protected function setUp(): void
    {
        $this->markTestIncomplete();

        $this->setApplicationConfig(
            include __DIR__ . '/../../../../../config/application.config.php'
        );
        parent::setUp();
    }

    protected function tearDown(): void
    {
        // FIXME: Drop database from in-memory
    }

    /**
     * @see https://github.com/zfcampus/zf-apigility/issues/18
     */
    public function testCreateReturnsRestServiceEntityWithControllerServiceNamePopulated(): void
    {
        $serviceManager = $this->getApplication()->getServiceManager();
        $em             = $serviceManager->get('doctrine.entitymanager.orm_default');

        new SchemaTool($em);

        // Create DB
        $resourceDefinition = [
            "objectManager"        => "doctrine.entitymanager.orm_default",
            "serviceName"          => "Artist",
            "entityClass"          => "Db\\Entity\\Artist",
            "routeIdentifierName"  => "artist_id",
            "entityIdentifierName" => "id",
            "routeMatch"           => "/db-test/artist",
        ];

        // Verify ORM is working
        $artist = new Artist();
        $artist->setName('TestInsert');
        $artist->setCreatedAt(new DateTime());
        $em->persist($artist);
        $em->flush();
        $found = $em->getRepository('Db\Entity\Artist')->find($artist->getId());
        $this->assertInstanceOf('Db\Entity\Artist', $found);

        $this->resource = $serviceManager->get(DoctrineRestServiceResource::class);
        $this->resource->setModuleName('DbApi');

        $entity = $this->resource->create($resourceDefinition);
        $this->assertInstanceOf(DoctrineRestServiceEntity::class, $entity);
        $controllerServiceName = $entity->controllerServiceName;
        $this->assertNotEmpty($controllerServiceName);
        $this->assertContains('DbApi\V1\Rest\Artist\Controller', $controllerServiceName);

        $request = $this->getRequest();
        $request->setMethod('GET');
        $request->getHeaders()->addHeaders(
            [
                'Accept' => 'application/json',
            ]
        );

        $this->dispatch('/db-api/artist');

        $this->resource->delete('DbApi\\V1\\Rest\\Artist\\Controller');
    }
}
