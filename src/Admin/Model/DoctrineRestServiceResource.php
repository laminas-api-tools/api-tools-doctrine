<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Admin\Model;

use Exception;
use Laminas\ApiTools\Admin\Model\DocumentationModel;
use Laminas\ApiTools\Admin\Model\InputFilterModel;
use Laminas\ApiTools\Admin\Model\RestServiceModel;
use Laminas\ApiTools\Admin\Model\RestServiceResource;
use Laminas\ApiTools\ApiProblem\ApiProblem;

use function is_object;
use function sprintf;

class DoctrineRestServiceResource extends RestServiceResource
{
    public function __construct(
        DoctrineRestServiceModelFactory $restFactory,
        InputFilterModel $inputFilterModel,
        DocumentationModel $documentationModel
    ) {
        parent::__construct($restFactory, $inputFilterModel, $documentationModel);
    }

    /**
     * Set module name
     *
     * @deprecated since 2.1.0, and no longer used internally.
     *
     * @param string $moduleName
     * @return DoctrineRestServiceResource
     */
    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;
        return $this;
    }

    /**
     * @param string $type
     * @return RestServiceModel
     */
    public function getModel($type = DoctrineRestServiceModelFactory::TYPE_DEFAULT)
    {
        if ($this->model instanceof DoctrineRestServiceModel) {
            return $this->model;
        }

        $moduleName  = $this->getModuleName();
        $this->model = $this->restFactory->factory($moduleName, $type);

        return $this->model;
    }

    /**
     * Create a new Doctrine REST service
     *
     * @param array|object $data
     * @return DoctrineRestServiceEntity|ApiProblem
     */
    public function create($data)
    {
        if (is_object($data)) {
            $data = (array) $data;
        }

        $type         = DoctrineRestServiceModelFactory::TYPE_DEFAULT;
        $creationData = new NewDoctrineServiceEntity();

        $creationData->exchangeArray($data);
        $model = $this->getModel($type);

        try {
            $service = $model->createService($creationData);
        } catch (Exception $e) {
            return new ApiProblem(
                409,
                sprintf('Unable to create Doctrine REST service: %s', $e->getMessage())
            );
        }

        return $service;
    }

    /**
     * Fetch Doctrine REST metadata
     *
     * @param string $id
     * @return DoctrineRestServiceEntity|ApiProblem
     */
    public function fetch($id)
    {
        $service = $this->getModel()->fetch($id);
        if (! $service instanceof DoctrineRestServiceEntity) {
            return new ApiProblem(404, 'Doctrine REST service not found');
        }

        return parent::fetch($id);
    }
}
