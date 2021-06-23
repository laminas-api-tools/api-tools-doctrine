<?php

declare(strict_types=1);

namespace LaminasTest\ApiTools\Doctrine\Admin\Model;

use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRestServiceResource;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineRpcServiceResource;
use Laminas\Http\Request;
use LaminasTest\ApiTools\Doctrine\TestCase;

use function json_decode;

class DoctrineMetadata2Test extends TestCase
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
    public function testDoctrineService(): void
    {
        $serviceManager = $this->getApplication()->getServiceManager();
        $serviceManager->get('doctrine.entitymanager.orm_default');

        $this->getRequest()->getHeaders()->addHeaders([
            'Accept' => 'application/json',
        ]);

        $this->dispatch(
            '/api-tools/api/module/DbApi/doctrine/DbApi%5CV1%5CRest%5CArtist%5CController',
            Request::METHOD_GET
        );
        $body = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('controller_service_name', $body);
        $this->assertEquals('DbApi\V1\Rest\Artist\Controller', $body['controller_service_name']);

        $this->dispatch('/api-tools/api/module/DbApi/doctrine?version=1', Request::METHOD_GET);
        $body = json_decode($this->getResponse()->getBody(), true);
        $this->assertEquals(
            'DbApi\V1\Rest\Artist\Controller',
            $body['_embedded']['doctrine'][0]['controller_service_name']
        );

        $this->dispatch('/api-tools/api/module/DbApi/doctrine', Request::METHOD_GET);
        $body = json_decode($this->getResponse()->getBody(), true);
        $this->assertEquals(
            'DbApi\V1\Rest\Artist\Controller',
            $body['_embedded']['doctrine'][0]['controller_service_name']
        );

        $this->resource = $serviceManager->get(DoctrineRestServiceResource::class);
        $this->resource->setModuleName('DbApi');
        $this->assertEquals($this->resource->getModuleName(), 'DbApi');

        $this->resource->patch(
            'DbApi\\V1\\Rest\\Artist\\Controller',
            [
                'routematch'             => '/doctrine-changed/test',
                'httpmethods'            => ['GET', 'POST', 'PUT'],
                'selector'               => 'new doctrine selector',
                'accept_whitelist'       => ['new whitelist accept'],
                'content_type_whitelist' => ['new content whitelist'],
            ]
        );

        $this->rpcResource = $serviceManager->get(DoctrineRpcServiceResource::class);
        $this->rpcResource->setModuleName('DbApi');
        $this->rpcResource->patch(
            'DbApi\\V1\\Rpc\\Artistalbum\\Controller',
            [
                'routematch'             => '/doctrine-rpc-changed/test',
                'httpmethods'            => ['GET', 'POST', 'PUT'],
                'selector'               => 'new selector',
                'accept_whitelist'       => ['new whitelist'],
                'content_type_whitelist' => ['new content whitelist'],
            ]
        );

        // Test get model returns cached model
        $this->assertEquals($this->rpcResource->getModel(), $this->rpcResource->getModel());
        $this->assertEquals($this->rpcResource->getModuleName(), $this->rpcResource->getModuleName());

        foreach ($body['_embedded']['doctrine'] as $service) {
            $this->resource->delete($service['controller_service_name']);
        }
        $this->dispatch('/api-tools/api/module/DbApi/doctrine-rpc?version=1', Request::METHOD_GET);
        $this->dispatch('/api-tools/api/module/DbApi/doctrine-rpc', Request::METHOD_GET);
        $body = json_decode($this->getResponse()->getBody(), true);
        $this->assertEquals(
            'DbApi\V1\Rpc\Artistalbum\Controller',
            $body['_embedded']['doctrine-rpc'][0]['controller_service_name']
        );

        foreach ($body['_embedded']['doctrine-rpc'] as $rpc) {
            $this->rpcResource->delete($rpc['controller_service_name']);
        }
    }
}
