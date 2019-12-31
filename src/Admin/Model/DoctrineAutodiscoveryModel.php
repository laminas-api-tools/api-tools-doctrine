<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-doctrine for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\Doctrine\Admin\Model;

use Laminas\ApiTools\Admin\Model\AbstractAutodiscoveryModel;

class DoctrineAutodiscoveryModel extends AbstractAutodiscoveryModel
{
    /**
     * Fetch fields for an adapter
     *
     * @param  string $module
     * @param  int    $version
     * @param  string $adapter_name
     * @return array
     */
    public function fetchFields($module, $version, $adapter_name)
    {
        $entities = array();

        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getServiceLocator()->get($adapter_name);

        /**
         * @var \Doctrine\ORM\Mapping\ClassMetadataFactory $cmf
         */
        $cmf = $em->getMetadataFactory();

        /**
         * @var \Doctrine\ORM\Mapping\ClassMetadata $classMetadata
         */
        foreach ($cmf->getAllMetadata() as $classMetadata) {
            $service = substr($classMetadata->getName(), strrpos($classMetadata->getName(), '\\') + 1);
            if ($this->moduleHasService($module, $version, $service)) {
                continue;
            }
            $entity = array(
                'entity_class' => $classMetadata->getName(),
                'service_name' => $service,
                'fields' => array(),
            );

            foreach ($classMetadata->fieldMappings as $mapping) {
                if ($classMetadata->isIdentifier($mapping['fieldName'])) {
                    continue;
                }
                $field = array(
                    'name' => $mapping['fieldName'],
                    'required' => (!isset($mapping['nullable']) || $mapping['nullable'] !== true),
                    'filters' => array(),
                    'validators' => array(),
                );
                switch ($mapping['type']) {
                    case 'string':
                        $field['filters'] = $this->filters['text'];
                        if (isset($mapping['length']) && $mapping['length']) {
                            $validator = $this->validators['text'];
                            $validator['options']['max'] = $mapping['length'];
                            $field['validators'][] = $validator;
                        }
                        break;
                    case 'integer':
                    case 'int':
                        $field['filters'] = $this->filters['integer'];
                        break;
                    default:
                        continue;
                        break;
                }
                $entity['fields'][] = $field;
            }

            $entities[] = $entity;
        }

        return $entities;
    }
}
