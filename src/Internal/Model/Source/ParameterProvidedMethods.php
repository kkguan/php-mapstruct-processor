<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Source;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Parameter;

class ParameterProvidedMethods
{
    /**
     * @var array<Parameter, array<SourceMethod>>
     */
    private array $parameterToProvidedMethods = [];

    /**
     * @var array<SourceMethod, Parameter>
     */
    private array $methodToProvidingParameter = [];

    /**
     * @param array<Parameter, array<SourceMethod>> $parameterToProvidedMethods
     */
    public function __construct(array $parameterToProvidedMethods)
    {
        $this->parameterToProvidedMethods = $parameterToProvidedMethods;
        foreach ($parameterToProvidedMethods as $index => $sourceMethods) {
            foreach ($sourceMethods as $method) {
                $this->methodToProvidingParameter[spl_object_hash($method)] = $index;
            }
        }
    }

    /**
     * @param array<Parameter> $orderedParameters
     * @return null|SourceMethod[]
     */
    public function getAllProvidedMethodsInParameterOrder(array $orderedParameters): ?array
    {
        $result = [];
        foreach ($orderedParameters as $parameter) {
            $methods = $this->parameterToProvidedMethods[spl_object_hash($parameter)] ?? [];
            if (! empty($methods)) {
                $result = $methods;
            }
        }

        return $result;
    }
}
