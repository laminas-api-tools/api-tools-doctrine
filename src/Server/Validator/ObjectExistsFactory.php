<?php

namespace Laminas\ApiTools\Doctrine\Server\Validator;

use DoctrineModule\Validator\ObjectExists;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\MutableCreationOptionsInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Stdlib\ArrayUtils;

class ObjectExistsFactory implements FactoryInterface, MutableCreationOptionsInterface
{
    /**
     * @var array
     */
    protected $options = array();

    /**
     * Create service
     *
     * @param  ServiceLocatorInterface $validators
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $validators)
    {
        if (isset($this->options['entity_class'])) {
            return new ObjectExists(
                ArrayUtils::merge(
                    $this->options,
                    array(
                    'object_repository' => $validators
                        ->getServiceLocator()
                        ->get('Doctrine\ORM\EntityManager')
                        ->getRepository($this->options['entity_class'])
                    )
                )
            );
        }
        return new ObjectExists($this->options);
    }

    /**
     * Set creation options
     *
     * @param  array $options
     * @return void
     */
    public function setCreationOptions(array $options)
    {
        $this->options = $options;
    }
}
