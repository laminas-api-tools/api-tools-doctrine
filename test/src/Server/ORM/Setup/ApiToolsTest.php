<?php
// Because of the code-generating of Laminas API Tools this script
// is used to setup the tests.  Use ~/test/bin/reset-tests
// to reset the output of this test if the unit tests
// fail the application.

namespace LaminasTest\ApiTools\Doctrine\Server\ORM\Setup;

use Doctrine\ORM\Tools\SchemaTool;
use Laminas\Filter\FilterChain;

class ApiToolsTest extends \Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../../../../config/ORM/application.config.php'
        );
        parent::setUp();
    }

    public function testBuildOrmApi()
    {
        $serviceManager = $this->getApplication()->getServiceManager();
        $em = $serviceManager->get('doctrine.entitymanager.orm_default');

        $tool = new SchemaTool($em);
        $res = $tool->createSchema($em->getMetadataFactory()->getAllMetadata());

        // Create DB
        $resource = $serviceManager->get('Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceResource');

        $artistResourceDefinition = array(
            "objectManager"=> "doctrine.entitymanager.orm_default",
            "serviceName" => "Artist",
            "entityClass" => "LaminasTestApiToolsDb\\Entity\\Artist",
            "routeIdentifierName" => "artist_id",
            "entityIdentifierName" => "id",
            "routeMatch" => "/test/artist",
            "collectionHttpMethods" => array(
                0 => 'GET',
                1 => 'POST',
                2 => 'PATCH',
                3 => 'DELETE',
            ),
        );

        $artistResourceDefinitionWithNonKeyIdentifer = array(
            "objectManager"=> "doctrine.entitymanager.orm_default",
            "serviceName" => "ArtistByName",
            "entityClass" => "LaminasTestApiToolsDb\\Entity\\Artist",
            "routeIdentifierName" => "artist_name",
            "entityIdentifierName" => "name",
            "routeMatch" => "/test/artist-by-name",
            "collectionHttpMethods" => array(
                0 => 'GET',
            ),
        );

        $albumResourceDefinition = array(
            "objectManager"=> "doctrine.entitymanager.orm_default",
            "serviceName" => "Album",
            "entityClass" => "LaminasTestApiToolsDb\\Entity\\Album",
            "routeIdentifierName" => "album_id",
            "entityIdentifierName" => "id",
            "routeMatch" => "/test/album",
            "collectionHttpMethods" => array(
                0 => 'GET',
                1 => 'POST',
                2 => 'PATCH',
                3 => 'DELETE',
            ),
        );

        $resource->setModuleName('LaminasTestApiToolsDbApi');
        $artistEntity = $resource->create($artistResourceDefinition);
        $artistEntity = $resource->create($artistResourceDefinitionWithNonKeyIdentifer);
        $albumEntity = $resource->create($albumResourceDefinition);

        $this->assertInstanceOf('Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceEntity', $artistEntity);
        $this->assertInstanceOf('Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceEntity', $albumEntity);

        // Build relation
        $filter = new FilterChain();
        $filter->attachByName('WordCamelCaseToUnderscore')
            ->attachByName('StringToLower');

        $em = $serviceManager->get('doctrine.entitymanager.orm_default');
        $metadataFactory = $em->getMetadataFactory();
        $entityMetadata = $metadataFactory->getMetadataFor("LaminasTestApiToolsDb\\Entity\\Artist");

        $rpcServiceResource = $serviceManager->get('Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRpcServiceResource');
        $rpcServiceResource->setModuleName('LaminasTestApiToolsDbApi');

        foreach ($entityMetadata->associationMappings as $mapping) {
            switch ($mapping['type']) {
                case 4:
                    $rpcServiceResource->create(
                        array(
                        'service_name' => 'Artist' . $mapping['fieldName'],
                        'route' => '/test/artist[/:parent_id]/' . $filter($mapping['fieldName']) . '[/:child_id]',
                        'http_methods' => array(
                            'GET', 'PUT', 'POST'
                            ),
                            'options' => array(
                            'target_entity' => $mapping['targetEntity'],
                            'source_entity' => $mapping['sourceEntity'],
                            'field_name' => $mapping['fieldName'],
                            ),
                            'selector' => 'custom selector',
                            )
                    );
                    break;
                default:
                    break;
            }
        }
    }
}