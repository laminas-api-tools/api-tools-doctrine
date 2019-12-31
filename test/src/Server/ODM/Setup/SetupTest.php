<?php
// Because of the code-generating of Laminas API Tools this script
// is used to setup the tests.  Use ~/test/bin/reset-tests
// to reset the output of this test if the unit tests
// fail the application.

namespace LaminasTest\ApiTools\Doctrine\Server\Model\Server\ODM\Setup;

class SetupTest extends \Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../../../../config/ODM/application.config.php'
        );

        parent::setUp();
    }

    public function testBuildOdmApi()
    {
        $serviceManager = $this->getApplication()->getServiceManager();

        // Create DB
        $resource = $serviceManager->get('Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceResource');

        $metaResourceDefinition = array(
            "objectManager"=> "doctrine.documentmanager.odm_default",
            "serviceName" => "Meta",
            "entityClass" => "LaminasTestApiToolsDbMongo\\Document\\Meta",
            "routeIdentifierName" => "meta_id",
            "entityIdentifierName" => "id",
            "routeMatch" => "/test/meta",
        );

        $resource->setModuleName('LaminasTestApiToolsDbMongoApi');
        $metaEntity = $resource->create($metaResourceDefinition);

        $this->assertInstanceOf('Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceEntity', $metaEntity);
    }
}
