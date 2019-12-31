<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-doctrine for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\Doctrine\Admin\Model;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;

class DoctrineAutodiscoveryModelFactory
{
    /**
     * @param ContainerInterface $container
     * @return DoctrineAutodiscoveryModel
     */
    public function __invoke(ContainerInterface $container)
    {
        if (! $container->has('config')) {
            throw new ServiceNotCreatedException(sprintf(
                'Cannot create %s service because config service is not present',
                DoctrineAutodiscoveryModel::class
            ));
        }

        $instance = new DoctrineAutodiscoveryModel($container->get('config'));
        $instance->setServiceLocator($container);

        return $instance;
    }
}
