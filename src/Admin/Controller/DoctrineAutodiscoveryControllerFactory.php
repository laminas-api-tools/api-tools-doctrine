<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-doctrine for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\Doctrine\Admin\Controller;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class DoctrineAutodiscoveryControllerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $controllers
     * @return DoctrineAutodiscoveryController
     */
    public function createService(ServiceLocatorInterface $controllers)
    {
        $services = $controllers->getServiceLocator();
        /** @var \Laminas\ApiTools\Doctrine\Admin\Model\DoctrineAutodiscoveryModel $model */
        $model = $services->get('Laminas\ApiTools\Doctrine\Admin\Model\DoctrineAutodiscoveryModel');
        return new DoctrineAutodiscoveryController($model);
    }
}
