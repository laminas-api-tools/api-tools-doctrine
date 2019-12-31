<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-doctrine for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\Doctrine\Admin\Model;

use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\ServiceManager\ServiceManager;
use Laminas\ServiceManager\ServiceManagerAwareInterface;

class DoctrineMetadataServiceResource extends AbstractResourceListener implements ServiceManagerAwareInterface
{
    protected $serviceManager;

    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }

    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * @codeCoverageIgnore
     */
    public function create($data)
    {
        throw new \Exception('Not Implemented');
    }

    /**
     * Fetch REST metadata
     *
     * @param  string                       $id
     * @return RestServiceEntity|ApiProblem
     */
    public function fetch($entityClassName)
    {
        $objectManagerAlias = $this->getEvent()->getRouteParam('object_manager_alias');

        if (!$objectManagerAlias) {
            // @codeCoverageIgnoreStart
            return new ApiProblem(500, 'No objectManager manager specificed in request.');
            // @codeCoverageIgnoreEnd
        }

        $objectManager = $this->getServiceManager()->get($objectManagerAlias);
        $metadataFactory = $objectManager->getMetadataFactory();

        $metadata = $metadataFactory->getMetadataFor($entityClassName);

        $entityClass = $this->getEntityClass();
        $metadataEntity = new $entityClass;
        $metadataEntity->exchangeArray((array) $metadata);

        return $metadataEntity;
    }

    /**
     * Fetch metadata for all REST services
     *
     * @param  array               $params
     * @return RestServiceEntity[]
     */
    public function fetchAll($params = array())
    {
        if ($this->getEvent()->getRouteParam('object_manager_alias')) {
            $objectManagerClass = $this->getEvent()->getRouteParam('object_manager_alias');
        }

        if (!$objectManagerClass) {
            // @codeCoverageIgnoreStart
            return new ApiProblem(500, 'No objectManager manager specificed in request.');
            // @codeCoverageIgnoreEnd
        }

        $objectManager = $this->getServiceManager()->get($objectManagerClass);
        $metadataFactory = $objectManager->getMetadataFactory();

        $return = [];
        foreach ($metadataFactory->getAllMetadata() as $metadata) {
            $entityClass = $this->getEntityClass();
            $metadataEntity = new $entityClass;
            $metadataEntity->exchangeArray((array) $metadata);

            $return[] = $metadataEntity;
        }

        return $return;
    }

    /**
     * @codeCoverageIgnore
     */
    public function patch($id, $data)
    {
        throw new \Exception('Not Implemented');
    }

    /**
     * @codeCoverageIgnore
     */
    public function delete($id)
    {
        throw new \Exception('Not Implemented');
    }
}
