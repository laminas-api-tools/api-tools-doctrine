<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Server\Validator;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator\ObjectExists;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Stdlib\ArrayUtils;
use Interop\Container\ContainerInterface;

class ObjectExistsFactory implements FactoryInterface
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
     * @return ObjectExists
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
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
     * @return ObjectExists
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, ObjectExists::class, $this->options);
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
