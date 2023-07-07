<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\ParameterBinding;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor\Accessor;

class ConstructorAccessor
{
    /** @var ParameterBinding[] */
    private array $parameterBindings = [];

    /** @var Accessor[] */
    private array $constructorAccessors = [];

    /**
     * @param ParameterBinding[] $parameterBindings
     * @param Accessor[] $constructorAccessors
     */
    public function __construct(array $parameterBindings, array $constructorAccessors)
    {
        $this->parameterBindings = $parameterBindings;
        $this->constructorAccessors = $constructorAccessors;
    }

    public function getParameterBindings(): array
    {
        return $this->parameterBindings;
    }

    public function getConstructorAccessors(): array
    {
        return $this->constructorAccessors;
    }
}
