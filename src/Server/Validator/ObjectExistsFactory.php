<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-doctrine for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\Doctrine\Server\Validator;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator\ObjectExists;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Stdlib\ArrayUtils;

class ObjectExistsFactory implements FactoryInterface
{
    /**
     * Required for v2 compatibility.
     *
     * @var array
     */
    protected $options = [];

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return ObjectExists
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (isset($options['entity_class'])) {
            $objectRepository = $container
                ->get(EntityManager::class)
                ->getRepository($options['entity_class']);

            $options = ArrayUtils::merge($options, ['object_repository' => $objectRepository]);
        }

        return new ObjectExists($options);
    }

    /**
     * Create and return an ObjectExists validator (v2).
     *
     * Proxies to `__invoke()`.
     *
     * @param ServiceLocatorInterface $container
     * @return ObjectExists
     */
    public function createService(ServiceLocatorInterface $container)
    {
        if ($container instanceof AbstractPluginManager) {
            $container = $container->getServiceLocator() ?: $container;
        }

        return $this($container, ObjectExists::class, $this->options);
    }

    /**
     * Allow injecting options at build time; required for v2 compatibility.
     *
     * @param array $options
     */
    public function setCreationOptions(array $options)
    {
        $this->options = $options;
    }
}
