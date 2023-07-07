<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Source;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Parameter;

class ParameterProvidedMethodsBuilder
{
    /**
     * @var array<Parameter, array<SourceMethod>>
     */
    private array $parameterToProvidedMethods = [];

    public function addMethodsForParameter(Parameter $parameter, array $methods): void
    {
        $this->parameterToProvidedMethods[spl_object_hash($parameter)] = $methods;
    }

    public function getParameterToProvidedMethods(): array
    {
        return $this->parameterToProvidedMethods;
    }

    public function setParameterToProvidedMethods(array $parameterToProvidedMethods): ParameterProvidedMethodsBuilder
    {
        $this->parameterToProvidedMethods = $parameterToProvidedMethods;
        return $this;
    }

    public function build()
    {
        return new ParameterProvidedMethods($this->getParameterToProvidedMethods());
    }
}
