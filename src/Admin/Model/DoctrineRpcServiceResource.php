<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Admin\Model;

use Exception;
use Laminas\ApiTools\Admin\Model\DocumentationModel;
use Laminas\ApiTools\Admin\Model\InputFilterModel;
use Laminas\ApiTools\Admin\Model\RpcServiceEntity;
use Laminas\ApiTools\Admin\Model\RpcServiceModel;
use Laminas\ApiTools\Admin\Model\RpcServiceResource;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\Rest\Exception\CreationException;
use Laminas\Mvc\Controller\ControllerManager;

use function is_array;
use function is_object;
use function is_string;

class DoctrineRpcServiceResource extends RpcServiceResource
{
    public function __construct(
        DoctrineRpcServiceModelFactory $rpcFactory,
        InputFilterModel $inputFilterModel,
        ControllerManager $controllerManager,
        DocumentationModel $documentationModel
    ) {
        parent::__construct($rpcFactory, $inputFilterModel, $controllerManager, $documentationModel);
    }

    /**
     * Set module name
     *
     * @deprecated since 2.1.0, and no longer used internally.
     *
     * @param string $moduleName
     * @return DoctrineRpcServiceResource
     */
    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;
        return $this;
    }

    /**
     * @return RpcServiceModel
     */
    public function getModel()
    {
        if ($this->model instanceof DoctrineRpcServiceModel) {
            return $this->model;
        }

        $moduleName  = $this->getModuleName();
        $this->model = $this->rpcFactory->factory($moduleName);

        return $this->model;
    }

    /**
     * Create a new Doctrine RPC service
     *
     * @param array|object $data
     * @return RpcServiceEntity|ApiProblem|false
     * @throws CreationException
     */
    public function create($data)
    {
        if (is_object($data)) {
            $data = (array) $data;
        }
        $creationData = [
            'http_methods' => ['GET'],
            'selector'     => null,
        ];

        if (
            empty($data['service_name'])
            || ! is_string($data['service_name'])
        ) {
            throw new CreationException('Unable to create RPC service; missing service_name');
        }

        $creationData['service_name'] = $data['service_name'];

        $model = $this->getModel();
        if ($model->fetch($creationData['service_name'])) {
            throw new CreationException('Service by that name already exists', 409);
        }

        if (
            empty($data['route_match'])
            || ! is_string($data['route_match'])
        ) {
            throw new CreationException('Unable to create RPC service; missing route');
        }
        $creationData['route_match'] = $data['route_match'];

        if (
            ! empty($data['http_methods'])
            && (is_string($data['http_methods']) || is_array($data['http_methods']))
        ) {
            $creationData['http_methods'] = $data['http_methods'];
        }

        if (
            ! empty($data['selector'])
            && is_string($data['selector'])
        ) {
            $creationData['selector'] = $data['selector'];
        }

        $creationData['options'] = (array) $data['options'];

        try {
            $service = $model->createService(
                $creationData['service_name'],
                $creationData['route_match'],
                $creationData['http_methods'],
                $creationData['selector'],
                $creationData['options']
            );
        } catch (Exception $e) {
            if ($e->getCode() !== 500) {
                return new ApiProblem($e->getCode(), $e->getMessage());
            }
            return new ApiProblem(500, 'Unable to create Doctrine RPC service');
        }

        return $service;
    }

    /**
     * Fetch Doctrine RPC metadata
     *
     * @param string $id
     * @return DoctrineRpcServiceEntity|ApiProblem
     */
    public function fetch($id)
    {
        $service = $this->getModel()->fetch($id);

        if (! $service instanceof DoctrineRpcServiceEntity) {
            return new ApiProblem(404, 'Doctrine RPC service not found');
        }

        return parent::fetch($id);
    }
}
