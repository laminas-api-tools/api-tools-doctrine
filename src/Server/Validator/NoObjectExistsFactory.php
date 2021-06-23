<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Server\Validator;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator\NoObjectExists;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Stdlib\ArrayUtils;

class NoObjectExistsFactory implements FactoryInterface
{
    /**
     * Required for v2 compatibility.
     *
     * @var array
     */
    protected $options = [];

    /**
     * @param string $requestedName
     * @param null|array $options
     * @return NoObjectExists
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        if (isset($options['entity_class'])) {
            $objectRepository = $container
                ->get(EntityManager::class)
                ->getRepository($options['entity_class']);

            $options = ArrayUtils::merge($options, ['object_repository' => $objectRepository]);
        }

        return new NoObjectExists($options);
    }

    /**
     * Create and return an NoObjectExists validator (v2).
     *
     * Proxies to `__invoke()`.
     *
     * @return NoObjectExists
     */
    public function createService(ServiceLocatorInterface $container)
    {
        if ($container instanceof AbstractPluginManager) {
            $container = $container->getServiceLocator() ?: $container;
        }

        return $this($container, NoObjectExists::class, $this->options);
    }

    /**
     * Allow injecting options at build time; required for v2 compatibility.
     *
     * @param array $options
     * @return void
     */
    public function setCreationOptions(array $options)
    {
        $this->options = $options;
    }
}
