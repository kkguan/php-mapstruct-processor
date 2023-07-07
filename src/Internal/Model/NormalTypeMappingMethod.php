<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Method;

abstract class NormalTypeMappingMethod extends MappingMethod
{
    protected $factoryMethod;

    protected bool $overridden;

    protected bool $mapNullToDefault;

    public function __construct(
        Method $method,
        array $existingVariableNames,
        $factoryMethod,
        bool $mapNullToDefault,
        array $beforeMappingReferences,
        array $afterMappingReferences
    ) {
        parent::__construct(method: $method, parameters: $method->getParameters(), existingVariableNames: $existingVariableNames, beforeMappingReferences: $beforeMappingReferences, afterMappingReferences: $afterMappingReferences);
        $this->factoryMethod = $factoryMethod;
        $this->overridden = $method->overridesMethod();
        $this->mapNullToDefault = $mapNullToDefault;
    }

    public function getFactoryMethod()
    {
        return $this->factoryMethod;
    }
}
