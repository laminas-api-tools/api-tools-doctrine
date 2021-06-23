<?php

declare(strict_types=1);

namespace LaminasTest\ApiTools\Doctrine\Admin\Model;

use Doctrine\ORM\Tools\SchemaTool;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceEntity;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceResource;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRpcServiceResource;
use Laminas\Filter\FilterChain;
use Laminas\Http\Request;
use LaminasTest\ApiTools\Doctrine\TestCase;

use function json_decode;
use function sprintf;

class DoctrineMetadata1Test extends TestCase
{
    protected function setUp(): void
    {
        $this->markTestIncomplete();

        $this->setApplicationConfig(
            include __DIR__ . '/../../../config/application.config.php'
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
    public function testDoctrineMetadataResource(): void
    {
        $serviceManager = $this->getApplication()->getServiceManager();
        $serviceManager->get('doctrine.entitymanager.orm_default');

        $this->getRequest()->getHeaders()->addHeaders([
            'Accept' => 'application/json',
        ]);

        $this->dispatch(
            '/api-tools/api/doctrine/doctrine.entitymanager.orm_default/metadata/Db%5CEntity%5CArtist',
            Request::METHOD_GET
        );
        $body = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('name', $body);
        $this->assertEquals('Db\Entity\Artist', $body['name']);

        $this->dispatch('/api-tools/api/doctrine/doctrine.entitymanager.orm_default/metadata', Request::METHOD_GET);
        $body = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('_embedded', $body);
    }

    public function testDoctrineService(): void
    {
        $serviceManager = $this->getApplication()->getServiceManager();
        $em             = $serviceManager->get('doctrine.entitymanager.orm_default');

        $tool = new SchemaTool($em);
        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());

        // Create DB
        $resourceDefinition = [
            "objectManager"        => "doctrine.entitymanager.orm_default",
            "serviceName"          => "Artist",
            "entityClass"          => "Db\\Entity\\Artist",
            "routeIdentifierName"  => "artist_id",
            "entityIdentifierName" => "id",
            "routeMatch"           => "/db-test/artist",
        ];

        $this->resource = $serviceManager->get(DoctrineRestServiceResource::class);
        $this->resource->setModuleName('DbApi');

        $entity = $this->resource->create($resourceDefinition);

        $this->assertInstanceOf(DoctrineRestServiceEntity::class, $entity);
        $controllerServiceName = $entity->controllerServiceName;
        $this->assertNotEmpty($controllerServiceName);
        $this->assertContains('DbApi\V1\Rest\Artist\Controller', $controllerServiceName);

        $filter = new FilterChain();
        $filter->attachByName('WordCamelCaseToUnderscore')
            ->attachByName('StringToLower');

        $em              = $serviceManager->get('doctrine.entitymanager.orm_default');
        $metadataFactory = $em->getMetadataFactory();
        $entityMetadata  = $metadataFactory->getMetadataFor("Db\\Entity\\Artist");

        foreach ($entityMetadata->associationMappings as $mapping) {
            switch ($mapping['type']) {
                case 4:
                    $rpcServiceResource = $serviceManager->get(
                        DoctrineRpcServiceResource::class
                    );
                    $rpcServiceResource->setModuleName('DbApi');
                    $rpcServiceResource->create([
                        'service_name' => 'Artist' . $mapping['fieldName'],
                        'route'        => sprintf(
                            '/db-test/artist[/:parent_id]/%s[/:child_id]',
                            $filter($mapping['fieldName'])
                        ),
                        'http_methods' => [
                            'GET',
                            'PUT',
                            'POST',
                        ],
                        'options'      => [
                            'target_entity' => $mapping['targetEntity'],
                            'source_entity' => $mapping['sourceEntity'],
                            'field_name'    => $mapping['fieldName'],
                        ],
                        'selector'     => 'custom selector',
                    ]);
                    break;
                default:
                    break;
            }
        }
    }
}
