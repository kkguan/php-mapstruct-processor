<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Method;

class AbstractBaseBuilder
{
    protected MappingBuilderContext $ctx;

    protected Method $method;

    protected $dependsOn;

    protected array $existingVariableNames = [];

    public function mappingContext(MappingBuilderContext $mappingContext)
    {
        $this->ctx = $mappingContext;
        return $this;
    }

    public function method(Method $sourceMethod): static
    {
        $this->method = $sourceMethod;
        return $this;
    }

    public function dependsOn($dependsOn): static
    {
        $this->dependsOn = $dependsOn;
        return $this;
    }

    public function existingVariableNames(array $existingVariableNames): static
    {
        $this->existingVariableNames = $existingVariableNames;
        return $this;
    }

    public function getExistingVariableNames(): array
    {
        return $this->existingVariableNames;
    }
}
