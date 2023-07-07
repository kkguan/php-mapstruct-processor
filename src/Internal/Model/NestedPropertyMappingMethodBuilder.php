<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model;

use Kkguan\PHPMapstruct\Processor\Exception\IllegalArgumentException;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Str;

class NestedPropertyMappingMethodBuilder
{
    private MappingBuilderContext $ctx;

    private ForgedMethod $method;

    private array $propertyEntries;

    public function setMappingContext(MappingBuilderContext $ctx): NestedPropertyMappingMethodBuilder
    {
        $this->ctx = $ctx;
        return $this;
    }

    public function setMethod(ForgedMethod $method): NestedPropertyMappingMethodBuilder
    {
        $this->method = $method;
        return $this;
    }

    public function setPropertyEntries(array $propertyEntries): NestedPropertyMappingMethodBuilder
    {
        $this->propertyEntries = $propertyEntries;
        return $this;
    }

    public function build(): NestedPropertyMappingMethod
    {
        $existingVariableNames = [];
        $sourceParameter = null;

        foreach ($this->method->getSourceParameters() as $parameter) {
            $existingVariableNames[] = $parameter->getName();
            if ($sourceParameter == null && ! $parameter->isMappingTarget() && ! $parameter->isMappingContext()) {
                $sourceParameter = $parameter;
            }
        }

        $thrownTypes = $safePropertyEntries = [];
        if ($sourceParameter == null) {
            throw new IllegalArgumentException('Method ' . $this->method->getName() . ' has no source parameter');
        }

        $previousPropertyName = $sourceParameter->getName();
        foreach ($this->propertyEntries as $propertyEntry) {
            $sageName = Str::getSafeVariableName($propertyEntry->getName(), $existingVariableNames);
            $safePropertyEntries[] = new SafePropertyEntry($propertyEntry, $sageName, $previousPropertyName);
            $existingVariableNames[] = $sageName;
            $previousPropertyName = $sageName;
        }
        // 增加异常信息
//        method.addThrownTypes( thrownTypes );
        return new NestedPropertyMappingMethod($this->method, $safePropertyEntries);
    }
}
