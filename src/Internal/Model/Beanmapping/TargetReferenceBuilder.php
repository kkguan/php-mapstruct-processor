<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Beanmapping;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\TypeFactory;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\MappingOptions;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\SourceMethod;

class TargetReferenceBuilder
{
    private MappingOptions $mapping;

    private string $targetName;

    private SourceMethod $method;

    private TypeFactory $typeFactory;

    private array $targetProperties;

    private Type $targetType;

    public function mapping(MappingOptions $mapping): static
    {
        $this->mapping = $mapping;
        $this->targetName = $mapping->getTargetName();
        return $this;
    }

    public function method(SourceMethod $method): static
    {
        $this->method = $method;
        return $this;
    }

    public function typeFactory(TypeFactory $typeFactory): static
    {
        $this->typeFactory = $typeFactory;
        return $this;
    }

    public function targetProperties(array $targetProperties): static
    {
        $this->targetProperties = $targetProperties;
        return $this;
    }

    public function targetType(Type $targetType): static
    {
        $this->targetType = $targetType;
        return $this;
    }

    public function build(): ?TargetReference
    {
        if (empty($this->targetName)) {
            return null;
        }

        $targetNameTrimmed = trim($this->targetName);
        $segments = explode('.', $targetNameTrimmed);
        $parameter = $this->method->getMappingTargetParameter();

        $targetPropertyNames = $segments;
        if (count($segments) > 1) {
            // 未实现相关功能
        }

        $entries = $targetPropertyNames;
        return new TargetReference(
            parameter: $parameter,
            propertyEntries: $entries,
        );
    }
}
