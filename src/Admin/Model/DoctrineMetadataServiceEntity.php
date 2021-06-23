<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Admin\Model;

use Laminas\Stdlib\ArraySerializableInterface;

class DoctrineMetadataServiceEntity implements ArraySerializableInterface
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $namespace;

    /** @var class-string */
    protected $rootEntityName;

    /** @var array */
    protected $customGeneratorDefinition;

    /** @var class-string */
    protected $customRepositoryClassName;

    /** @var bool */
    protected $isMappedSuperclass;

    /** @var string[] */
    protected $parentClasses;

    /** @var string[] */
    protected $subClasses;

    /** @var array */
    protected $namedQueries;

    /** @var array */
    protected $namedNativeQueries;

    /** @var array */
    protected $sqlResultSetMappings;

    /** @var string */
    protected $identifier;

    /** @var string */
    protected $inheritanceType;

    /** @var string */
    protected $generatorType;

    /** @var array */
    protected $fieldMappings;

    /** @var string[] */
    protected $fieldNames;

    /** @var string[] */
    protected $columnNames;

    /** @var string */
    protected $discriminatorValue;

    /** @var array */
    protected $discriminatorMap;

    /** @var string */
    protected $discriminatorColumn;

    /** @var string */
    protected $table;

    /** @var array */
    protected $lifecycleCallbacks;

    /** @var array */
    protected $entityListeners;

    /** @var array */
    protected $associationMappings;

    /** @var bool */
    protected $isIdentifierComposite;

    /** @var bool */
    protected $containsForeignIdentifier;

    /** @var string */
    protected $idGenerator;

    /** @var array */
    protected $sequenceGeneratorDefinition;

    /** @var array */
    protected $tableGeneratorDefinition;

    /** @var string */
    protected $changeTrackingPolicy;

    /** @var bool */
    protected $isVersioned;

    /** @var string */
    protected $versionField;

    /** @var class-string */
    protected $reflClass;

    /** @var bool */
    protected $isReadOnly;

    /** @var string */
    protected $namingStrategy;

    /** @var array */
    protected $reflFields;

    /** @var mixed */
    protected $classMetadataInfoPrototype;

    /** @return $this */
    public function exchangeArray(array $data)
    {
        foreach ($data as $field => $value) {
            switch ($field) {
                case 'name':
                    $this->name = $value;
                    break;
                case 'namespace':
                    $this->namespace = $value;
                    break;
                case 'rootEntityName':
                    $this->rootEntityName = $value;
                    break;
                case 'customGeneratorDefinition':
                    $this->customGeneratorDefinition = $value;
                    break;
                case 'customRepositoryClassName':
                    $this->customRepositoryClassName = $value;
                    break;
                case 'isMappedSuperclass':
                    $this->isMappedSuperclass = $value;
                    break;
                case 'parentClasses':
                    $this->parentClasses = $value;
                    break;
                case 'subClasses':
                    $this->subClasses = $value;
                    break;
                case 'namedQueries':
                    $this->namedQueries = $value;
                    break;
                case 'namedNativeQueries':
                    $this->namedNativeQueries = $value;
                    break;
                case 'sqlResultSetMappings':
                    $this->sqlResultSetMappings = $value;
                    break;
                case 'identifier':
                    $this->identifier = $value;
                    break;
                case 'inheritanceType':
                    $this->inheritanceType = $value;
                    break;
                case 'generatorType':
                    $this->generatorType = $value;
                    break;
                case 'fieldMappings':
                    $this->fieldMappings = $value;
                    break;
                case 'fieldNames':
                    $this->fieldNames = $value;
                    break;
                case 'columnNames':
                    $this->columnNames = $value;
                    break;
                case 'discriminatorValue':
                    $this->discriminatorValue = $value;
                    break;
                case 'discriminatorMap':
                    $this->discriminatorMap = $value;
                    break;
                case 'discriminatorColumn':
                    $this->discriminatorColumn = $value;
                    break;
                case 'table':
                    $this->table = $value;
                    break;
                case 'lifecycleCallbacks':
                    $this->lifecycleCallbacks = $value;
                    break;
                case 'entityListeners':
                    $this->entityListeners = $value;
                    break;
                case 'associationMappings':
                    $this->associationMappings = $value;
                    break;
                case 'isIdentifierComposite':
                    $this->isIdentifierComposite = $value;
                    break;
                case 'containsForeignIdentifier':
                    $this->containsForeignIdentifier = $value;
                    break;
                case 'idGenerator':
                    $this->idGenerator = $value;
                    break;
                case 'sequenceGeneratorDefinition':
                    $this->sequenceGeneratorDefinition = $value;
                    break;
                case 'tableGeneratorDefinition':
                    $this->tableGeneratorDefinition = $value;
                    break;
                case 'changeTrackingPolicy':
                    $this->changeTrackingPolicy = $value;
                    break;
                case 'isVersioned':
                    $this->isVersioned = $value;
                    break;
                case 'versionField':
                    $this->versionField = $value;
                    break;
                case 'reflClass':
                    $this->reflClass = $value;
                    break;
                case 'isReadOnly':
                    $this->isReadOnly = $value;
                    break;
                case '*namingStrategy':
                    $this->namingStrategy = $value;
                    break;
                case 'reflFields':
                    $this->reflFields = $value;
                    break;
                case 'Doctrine\ORM\Mapping\ClassMetadataInfo_prototype':
                    $this->classMetadataInfoPrototype = $value;
                    break;
                default:
                    break;
            }
        }

        return $this;
    }

    /** @return array<string, mixed> */
    public function getArrayCopy()
    {
        return [
            'name'                                             => $this->name,
            'namespace'                                        => $this->namespace,
            'rootEntityName'                                   => $this->rootEntityName,
            'customGeneratorDefinition'                        => $this->customGeneratorDefinition,
            'customRepositoryClassName'                        => $this->customRepositoryClassName,
            'isMappedSuperclass'                               => $this->isMappedSuperclass,
            'parentClasses'                                    => $this->parentClasses,
            'subClasses'                                       => $this->subClasses,
            'namedQueries'                                     => $this->namedQueries,
            'namedNativeQueries'                               => $this->namedNativeQueries,
            'sqlResultSetMappings'                             => $this->sqlResultSetMappings,
            'identifier'                                       => $this->identifier,
            'inheritanceType'                                  => $this->inheritanceType,
            'generatorType'                                    => $this->generatorType,
            'fieldMappings'                                    => $this->fieldMappings,
            'fieldNames'                                       => $this->fieldNames,
            'columnNames'                                      => $this->columnNames,
            'discriminatorValue'                               => $this->discriminatorValue,
            'discriminatorMap'                                 => $this->discriminatorMap,
            'discriminatorColumn'                              => $this->discriminatorColumn,
            'table'                                            => $this->table,
            'lifecycleCallbacks'                               => $this->lifecycleCallbacks,
            'entityListeners'                                  => $this->entityListeners,
            'associationMappings'                              => $this->associationMappings,
            'isIdentifierComposite'                            => $this->isIdentifierComposite,
            'containsForeignIdentifier'                        => $this->containsForeignIdentifier,
            'idGenerator'                                      => $this->idGenerator,
            'sequenceGeneratorDefinition'                      => $this->sequenceGeneratorDefinition,
            'tableGeneratorDefinition'                         => $this->tableGeneratorDefinition,
            'changeTrackingPolicy'                             => $this->changeTrackingPolicy,
            'isVersioned'                                      => $this->isVersioned,
            'versionField'                                     => $this->versionField,
            'reflClass'                                        => $this->reflClass,
            'isReadOnly'                                       => $this->isReadOnly,
            '*namingStrategy'                                  => $this->namingStrategy,
            'reflFields'                                       => $this->reflFields,
            'Doctrine\ORM\Mapping\ClassMetadataInfo_prototype' => $this->classMetadataInfoPrototype,
        ];
    }
}
