<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Selector;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\ParameterBinding;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Method;

/**
 * A selected method with additional metadata that might be required for further usage of the selected method.
 */
class SelectedMethod
{
    /**
     * @var ParameterBinding[]
     */
    private array $parameterBindings;

    public function __construct(private Method $method)
    {
    }

    public function getMethod(): Method
    {
        return $this->method;
    }

    /**
     * @return ParameterBinding[]
     */
    public function getParameterBindings(): array
    {
        return $this->parameterBindings;
    }

    /**
     * @param ParameterBinding[] $parameterBindings
     */
    public function setParameterBindings(array $parameterBindings): SelectedMethod
    {
        $this->parameterBindings = $parameterBindings;
        return $this;
    }
}
