<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Beanmapping\MappingReferences;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Parameter;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Method;

class BeanMappingMethod extends NormalTypeMappingMethod
{
    /** @var PropertyMapping[] */
    private array $propertyMappings;

    private $returnTypeBuilder;

    private $finalizerMethod;

    private ?MappingReferences $mappingReferences;

    /** @var PropertyMapping[] */
    private array $mappingsByParameter;

    /** @var PropertyMapping[] */
    private array $constantMappings;

    /** @var PropertyMapping[] */
    private array $constructorMappingsByParameter;

    /** @var PropertyMapping[] */
    private array $constructorConstantMappings;

    private ?Type $returnTypeToConstruct;

    private array $subclassMappings;

    /**
     * @param PropertyMapping[] $propertyMappings
     * @param mixed $factoryMethod
     * @param mixed $returnTypeBuilder
     * @param mixed $finalizerMethod
     */
    public function __construct(
        Method $method,
        array $existingVariableNames,
        array $propertyMappings,
        $factoryMethod,
        bool $mapNullToDefault,
        ?Type $returnTypeToConstruct,
        $returnTypeBuilder,
        array $beforeMappingReferences,
        array $afterMappingReferences,
        $finalizerMethod,
        ?MappingReferences $mappingReferences,
        array $subclassMappings
    ) {
        parent::__construct(
            method: $method,
            existingVariableNames: $existingVariableNames,
            factoryMethod: $factoryMethod,
            mapNullToDefault: $mapNullToDefault,
            beforeMappingReferences: $beforeMappingReferences,
            afterMappingReferences: $afterMappingReferences
        );

        $this->propertyMappings = $propertyMappings;
        $this->returnTypeBuilder = $returnTypeBuilder;
        $this->finalizerMethod = $finalizerMethod;
        $this->mappingReferences = $mappingReferences;

        $this->mappingsByParameter = [];
        $this->constantMappings = [];
        $this->constructorMappingsByParameter = [];
        $this->constructorConstantMappings = [];

        $sourceParameterNames = [];
        foreach ($this->getSourceParameters() as $sourceParameter) {
            $sourceParameterNames[$sourceParameter->getName()][] = $sourceParameter;
        }

        foreach ($propertyMappings as $mapping) {
            if ($mapping->isConstructorMapping()) {
                if (isset($sourceParameterNames[$mapping->getSourceBeanName()])) {
                    $this->constructorMappingsByParameter[$mapping->getSourceBeanName()] = $mapping;
                } else {
                    $this->constructorConstantMappings[] = $mapping;
                }
            } elseif (isset($sourceParameterNames[$mapping->getSourceBeanName()])) {
                $this->mappingsByParameter[$mapping->getSourceBeanName()][] = $mapping;
            } else {
                $this->constantMappings[] = $mapping;
            }
        }

        $this->returnTypeToConstruct = $returnTypeToConstruct;
        $this->subclassMappings = $subclassMappings;
    }

    public function getSubclassMappings(): array
    {
        return $this->subclassMappings;
    }

    public function hasSubclassMappings(): bool
    {
        return ! empty($this->subclassMappings);
    }

    public function isAbstractReturnType(): bool
    {
        return $this->getFactoryMethod() == null && $this->returnTypeToConstruct != null && $this->returnTypeToConstruct->isAbstract();
    }

    public function hasConstructorMappings(): bool
    {
        return ! empty($this->constructorMappingsByParameter) || ! empty($this->constructorConstantMappings);
    }

    public function getTemplate()
    {
        return 'BeanMappingMethod.twig';
    }

    public function getSourceParametersNeedingNullCheck(): array
    {
        $list = [];
        foreach ($this->getSourceParameters() as $parameter) {
            if ($this->needsNullCheck($parameter)) {
                $list[] = $parameter;
            }
        }
        return $list;
    }

    /**
     * @return PropertyMapping[]
     */
    public function propertyMappingsByParameter(Parameter $parameter): array
    {
        return $this->mappingsByParameter[$parameter->getName()] ?? [];
    }

    private function needsNullCheck(Parameter $parameter): bool
    {
        if ($parameter->getType()->isPrimitive()) {
            return false;
        }

        $mappings = $this->propertyMappingsByParameter($parameter);

        if (count($mappings) == 1 && $this->doesNotNeedNullCheckForSourceParameter($mappings[0])) {
            return false;
        }

        return true;
    }

    private function doesNotNeedNullCheckForSourceParameter(PropertyMapping $mapping): bool
    {
        if ($mapping->getAssignment()->isCallingUpdateMethod()) {
            // If the mapping assignment is calling an update method then we should do a null check
            // in the bean mapping
            return false;
        }

        return $mapping->getAssignment()->isSourceReferenceParameter();
    }
}
