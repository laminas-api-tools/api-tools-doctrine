<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Admin\Model;

use Doctrine\Common\Persistence\Mapping\AbstractClassMetadataFactory;
use Exception;
use Laminas\ApiTools\Admin\Model\RestServiceEntity;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\ServiceManager\ServiceManager;

class DoctrineMetadataServiceResource extends AbstractResourceListener
{
    /** @var ServiceManager */
    protected $serviceManager;

    /**
     * @return $this
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }

    /**
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * @param array $data
     * @psalm-return never
     * @throws Exception Always.
     */
    public function create($data)
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Fetch REST metadata
     *
     * @param string $entityClassName
     * @return RestServiceEntity|ApiProblem
     */
    public function fetch($entityClassName)
    {
        $objectManagerAlias = $this->getEvent()->getRouteParam('object_manager_alias');

        if (! $objectManagerAlias) {
            return new ApiProblem(500, 'No objectManager manager specified in request.');
        }

        $objectManager = $this->getServiceManager()->get($objectManagerAlias);
        /** @var AbstractClassMetadataFactory $metadataFactory */
        $metadataFactory = $objectManager->getMetadataFactory();

        $metadata = $metadataFactory->getMetadataFor($entityClassName);

        $entityClass    = $this->getEntityClass();
        $metadataEntity = new $entityClass();
        $metadataEntity->exchangeArray((array) $metadata);

        return $metadataEntity;
    }

    /**
     * Fetch metadata for all REST services
     *
     * @param array $params
     * @return RestServiceEntity[]|ApiProblem
     */
    public function fetchAll($params = [])
    {
        if ($this->getEvent()->getRouteParam('object_manager_alias')) {
            $objectManagerClass = $this->getEvent()->getRouteParam('object_manager_alias');
        }

        if (empty($objectManagerClass)) {
            return new ApiProblem(500, 'No objectManager manager specified in request.');
        }

        $objectManager = $this->getServiceManager()->get($objectManagerClass);
        /** @var AbstractClassMetadataFactory $metadataFactory */
        $metadataFactory = $objectManager->getMetadataFactory();

        $return = [];
        foreach ($metadataFactory->getAllMetadata() as $metadata) {
            $entityClass    = $this->getEntityClass();
            $metadataEntity = new $entityClass();
            $metadataEntity->exchangeArray((array) $metadata);

            $return[] = $metadataEntity;
        }

        return $return;
    }

    /**
     * @param int|string $id
     * @param array $data
     * @psalm-return never
     * @throws Exception Always.
     */
    public function patch($id, $data)
    {
        throw new Exception('Not Implemented');
    }

    /**
     * @param int|string $id
     * @psalm-return never
     * @throws Exception Always.
     */
    public function delete($id)
    {
        throw new Exception('Not Implemented');
    }
}
