<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Admin\Controller;

use Interop\Container\ContainerInterface;
use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineAutodiscoveryModel;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class DoctrineAutodiscoveryControllerFactory implements FactoryInterface
{
    /**
     * Create and return DoctrineAutodiscoveryController instance.
     *
     * @param string $requestedName
     * @param null|array $options
     * @return DoctrineAutodiscoveryController
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        /** @var DoctrineAutodiscoveryModel $model */
        $model = $container->get(DoctrineAutodiscoveryModel::class);

        return new DoctrineAutodiscoveryController($model);
    }

    /**
     * Create and return DoctrineAutodiscoveryController instance (v2).
     *
     * Provided for backwards compatibility; proxies to __invoke().
     *
     * @return DoctrineAutodiscoveryController
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, DoctrineAutodiscoveryController::class);
    }
}
