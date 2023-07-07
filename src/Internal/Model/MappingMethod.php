<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Parameter;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Method;

abstract class MappingMethod
{
    private string $name;

    /** @var Parameter[] */
    private array $parameters;

    /**
     * @var Parameter[]
     */
    private array $sourceParameters;

    private Common\Type $returnType;

    private ?Parameter $targetParameter;

    private Source\Accessibility $accessibility;

    private bool $isStatic;

    public function __construct(
        Method $method,
        array $parameters,
        array $existingVariableNames,
        array $beforeMappingReferences,
        array $afterMappingReferences
    ) {
        $this->name = $method->getName();
        $this->parameters = $parameters;
        $this->sourceParameters = Parameter::getSourceParameters($parameters);
        $this->returnType = $method->getReturnType();
        $this->targetParameter = $method->getMappingTargetParameter();
        $this->accessibility = $method->getAccessibility();
//        $this->thrownTypes = $method->getThrownTypes();
        $this->isStatic = $method->isStatic();
        $this->resultName = $this->initResultName($existingVariableNames);
//        $this->beforeMappingReferencesWithMappingTarget = filterMappingTarget(beforeMappingReferences, true);
//        $this->beforeMappingReferencesWithoutMappingTarget = filterMappingTarget(beforeMappingReferences, false);
//        $this->afterMappingReferences = afterMappingReferences == null ? Collections . emptyList() : afterMappingReferences;
    }

    public function getSourceParameters(): array
    {
        return $this->sourceParameters;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getReturnType(): Common\Type
    {
        return $this->returnType;
    }

    public function getTargetParameter(): ?Parameter
    {
        return $this->targetParameter;
    }

    public function getAccessibility(): Source\Accessibility
    {
        return $this->accessibility;
    }

    public function isStatic(): bool
    {
        return $this->isStatic;
    }

    public function isExistingInstanceMapping(): bool
    {
        return $this->targetParameter != null;
    }

    public function getResultName()
    {
        return $this->resultName;
    }

    private function initResultName(array $existingVariableNames): string
    {
        $resultName = 'result';
        $i = 0;
        while (in_array($resultName, $existingVariableNames)) {
            $resultName = 'result' . $i;
            ++$i;
        }
        return $resultName;
    }
}
