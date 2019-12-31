<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-doctrine for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\Doctrine\Admin\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Laminas\ApiTools\Admin\Model\NewRestServiceEntity as LaminasNewRestServiceEntity;
use Laminas\Stdlib\ArraySerializableInterface;

class NewDoctrineServiceEntity extends LaminasNewRestServiceEntity implements ArraySerializableInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $hydratorName;

    /**
     * @var boolean
     */
    protected $byValue = true;

    /**
     * @var array
     */
    protected $hydratorStrategies = array();

    /**
     * @var boolean
     */
    protected $useGeneratedHydrator = true;

    public function exchangeArray(array $data)
    {
        parent::exchangeArray($data);
        foreach ($data as $key => $value) {
            $key = strtolower($key);
            $key = str_replace('_', '', $key);
            switch ($key) {
                case 'objectmanager':
                    $this->objectManager = $value;
                    break;
                case 'hydrator':
                    $this->hydratorName = $value;
                    break;
                case 'byvalue':
                    $this->byValue = $value;
                    break;
                case 'hydratorstrategies':
                    $this->hydratorStrategies = $value;
                    break;
                case 'usegeneratedhydrator':
                    $this->useGeneratedHydrator = $value;
                    break;
                default:
                    break;
            }
        }
    }

    public function getArrayCopy()
    {
        $data = parent::getArrayCopy();
        $data['object_manager']         = $this->objectManager;
        $data['hydrator_name']          = $this->hydratorName;
        $data['by_value']               = $this->byValue;
        $data['entity_identifier_name'] = $this->entityIdentifierName;
        $data['strategies']             = $this->hydratorStrategies;
        $data['use_generated_hydrator'] = $this->useGeneratedHydrator;

        return $data;
    }
}
