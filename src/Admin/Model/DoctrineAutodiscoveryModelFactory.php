<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Admin\Model;

use interop\container\containerinterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;

use function sprintf;

class DoctrineAutodiscoveryModelFactory
{
    /**
     * @return DoctrineAutodiscoveryModel
     */
    public function __invoke(containerinterface $container)
    {
        if (! $container->has('config')) {
            throw new ServiceNotCreatedException(sprintf(
                'Cannot create %s service because config service is not present',
                DoctrineAutodiscoveryModel::class
            ));
        }

        /** @var array $config */
        $config = $container->get('config');

        $instance = new DoctrineAutodiscoveryModel($config);
        $instance->setServiceLocator($container);

        return $instance;
    }
}
