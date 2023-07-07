<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Beanmapping\MappingReferences;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Parameter;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\AbstractMethod;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Accessibility;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\MappingMethodOptions;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Method;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Str;

class ForgedMethod extends AbstractMethod
{
    /**
     * @var Parameter[]
     */
    private array $parameters = [];

    /**
     * @var Parameter[]
     */
    private array $sourceParameters = [];

    private array $contextParameters = [];

    private ?Parameter $mappingTargetParameter;

    private string $name;

    private MappingMethodOptions $options;

    private Type $sourceType;

    /**
     * @param Parameter[] $additionalParameters
     */
    public function __construct(
        string $name,
        Type $sourceType,
        private Type $returnType,
        private ?array $additionalParameters = null,
        private Method $baseOn,
        private ?ForgedMethodHistory $history,
        private MappingReferences $mappingReferences,
        private bool $forgedNameBased
    ) {
        // establish name
        if (empty($this->additionalParameters)) {
            $sourceParamSafeName = Str::getSafeVariableName($sourceType->getName());
        } else {
            $list = [];
            foreach ($this->additionalParameters as $parameter) {
                $list[] = $parameter->getName();
            }
            $sourceParamSafeName = Str::getSafeVariableName($sourceType->getName(), $list);
        }

        $this->sourceType = $sourceType;

        $sourceParameter = new Parameter(
            element: null,
            type: $sourceType,
            name: $sourceParamSafeName,
        );

        $this->parameters = array_merge($this->parameters, [$sourceParameter]);
        $this->sourceParameters = Parameter::getSourceParameters($this->parameters);
        $this->contextParameters = Parameter::getContextParameters($this->parameters);
        $this->mappingTargetParameter = Parameter::getMappingTargetParameter($this->parameters);
        // based on method
        $this->name = Str::sanitizeIdentifierName($name);
        $this->options = MappingMethodOptions::getForgedMethodInheritedOptions($this->baseOn->getOptions());
    }

    public function __toString(): string
    {
        $str = $this->returnType->getName();
        $parametersName = [];
        foreach ($this->parameters as $parameter) {
            $parametersName[] = $parameter->getName();
        }

        return sprintf('%s %s(%s)', $str, $this->name, implode(', ', $parametersName));
    }

    public function getOptions(): MappingMethodOptions
    {
        return $this->options;
    }

    public function isLifecycleCallbackMethod(): bool
    {
        return false;
    }

    public function isUpdateMethod(): bool
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAccessibility(): Accessibility
    {
        return new Accessibility(Accessibility::PROTECTED);
    }

    public function isStatic(): bool
    {
        return false;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function overridesMethod(): bool
    {
        return false;
    }

    public function getReturnType(): ?Type
    {
        return $this->returnType;
    }

    public function isObjectFactory(): bool
    {
        // TODO: Implement isObjectFactory() method.
    }

    public function isPresenceCheck(): bool
    {
        // TODO: Implement isPresenceCheck() method.
    }

    /**
     * @return Parameter[]
     */
    public function getSourceParameters(): array
    {
        return $this->sourceParameters;
    }

    public function setSourceParameters(array $sourceParameters): ForgedMethod
    {
        $this->sourceParameters = $sourceParameters;
        return $this;
    }

    public function getContextParameters(): array
    {
        return $this->contextParameters;
    }

    public function setContextParameters(array $contextParameters): ForgedMethod
    {
        $this->contextParameters = $contextParameters;
        return $this;
    }

    public function getMappingTargetParameter(): ?Parameter
    {
        return $this->mappingTargetParameter;
    }

    public function setMappingTargetParameter(Parameter $mappingTargetParameter): ForgedMethod
    {
        $this->mappingTargetParameter = $mappingTargetParameter;
        return $this;
    }

    public function getSourceType(): Type
    {
        return $this->sourceType;
    }

    public function setSourceType(Type $sourceType): ForgedMethod
    {
        $this->sourceType = $sourceType;
        return $this;
    }

    public function getAdditionalParameters(): ?array
    {
        return $this->additionalParameters;
    }

    public function setAdditionalParameters(?array $additionalParameters): ForgedMethod
    {
        $this->additionalParameters = $additionalParameters;
        return $this;
    }

    public function getBaseOn(): Method
    {
        return $this->baseOn;
    }

    public function setBaseOn(Method $baseOn): ForgedMethod
    {
        $this->baseOn = $baseOn;
        return $this;
    }

    public function getHistory(): ForgedMethodHistory
    {
        return $this->history;
    }

    public function setHistory(ForgedMethodHistory $history): ForgedMethod
    {
        $this->history = $history;
        return $this;
    }

    public function getMappingReferences(): MappingReferences
    {
        return $this->mappingReferences;
    }

    public function setMappingReferences(MappingReferences $mappingReferences): ForgedMethod
    {
        $this->mappingReferences = $mappingReferences;
        return $this;
    }

    public function isForgedNameBased(): bool
    {
        return $this->forgedNameBased;
    }

    public function setForgedNameBased(bool $forgedNameBased): ForgedMethod
    {
        $this->forgedNameBased = $forgedNameBased;
        return $this;
    }

    public function isDefault(): bool
    {
        return false;
    }

    public function getTemplate()
    {
        return '111.twig';
    }
}
