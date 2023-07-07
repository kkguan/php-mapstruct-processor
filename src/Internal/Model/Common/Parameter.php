<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Common;

use Kkguan\PHPMapstruct\Processor\Internal\Gem\MappingTargetGem;

class Parameter
{
    private ?string $name;

    private string $originalName;

    private bool $mappingTarget;

    // 未支持
    private bool $targetType = false;

    // 未支持
    private bool $mappingContext = false;

    private ?\ReflectionParameter $element = null;

    private ?Type $type;

    public function __construct(null|\ReflectionParameter $element = null, null|Type $type = null, null|string $name = null)
    {
        $this->element = $element;
        $this->type = $type;
        if (empty($name)) {
            $this->name = $element->getName();
            $this->originalName = $this->name;
        } else {
            $this->name = $name;
            $this->originalName = $name;
        }

        if (empty($element)) {
            $this->mappingTarget = false;
        } else {
            $this->mappingTarget = MappingTargetGem::instanceOn($element) != null;
        }
    }

    public function isMappingTarget(): bool
    {
        return $this->mappingTarget;
    }

    public function isMappingContext(): bool
    {
        return $this->mappingContext;
    }

    public function isTargetType(): bool
    {
        return $this->targetType;
    }

    public static function forElementAndType(\ReflectionParameter $element, ?Type $parameterType)
    {
        return new self(
            element: $element,
            type: $parameterType,
            name: null
        );
    }

    /**
     * @param Parameter[] $parameters
     * @return Parameter[]
     */
    public static function getSourceParameters(array $parameters): array
    {
        $sourceParameters = [];
        foreach ($parameters as $parameter) {
            if (static::isSourceParameter($parameter)) {
                $sourceParameters[] = $parameter;
            }
        }
        return $sourceParameters;
    }

    /**
     * @param Parameter[] $parameters
     * @return Parameter[]
     */
    public static function getContextParameters(array $parameters): array
    {
        $contextParameters = [];
        foreach ($parameters as $parameter) {
            if ($parameter->isMappingContext()) {
                $contextParameters[] = $parameter;
            }
        }
        return $contextParameters;
    }

    /**
     * @param Parameter[] $parameters
     */
    public static function getMappingTargetParameter(array $parameters): ?Parameter
    {
        foreach ($parameters as $parameter) {
            if ($parameter->isMappingTarget()) {
                return $parameter;
            }
        }
        return null;
    }

    /**
     * @param Parameter[] $parameters
     */
    public static function getTargetTypeParameter(array $parameters): ?Parameter
    {
        foreach ($parameters as $parameter) {
            if ($parameter->isTargetType()) {
                return $parameter;
            }
        }
        return null;
    }

    /**
     * @param Parameter[] $parameters
     */
    public static function getSourceParameter(array $parameters, string $sourceParameterName): ?Parameter
    {
        foreach ($parameters as $parameter) {
            if (static::isSourceParameter($parameter) && ($parameter->getName() == $sourceParameterName)) {
                return $parameter;
            }
        }
        return null;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getElement(): ?\ReflectionParameter
    {
        return $this->element;
    }

    public function isPHPLangType()
    {
        return $this->type->isPHPLangType();
    }

    public function getTemplate(): string
    {
        return 'Common/parameter.twig';
    }

    private static function isSourceParameter(Parameter $parameter): bool
    {
        return ! $parameter->isMappingTarget() && ! $parameter->isTargetType() && ! $parameter->isMappingContext();
    }
}
