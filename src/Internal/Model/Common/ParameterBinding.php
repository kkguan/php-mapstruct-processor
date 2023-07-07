<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Common;

class ParameterBinding
{
    private function __construct(
        private Type $type,
        private ?string $variableName,
        private bool $targetType,
        private bool $mappingTarget,
        private bool $mappingContext,
        private ?SourceRHS $sourceRHS,
    ) {
    }

    public static function fromTypeAndName(Type $parameterType, string $parameterName)
    {
        return new ParameterBinding(
            $parameterType,
            $parameterName,
            false,
            false,
            false,
            null
        );
    }

    public static function forTargetType(Type $parameterType, string $parameterName)
    {
        return new ParameterBinding(
            $parameterType,
            $parameterName,
            true,
            false,
            false,
            null
        );
    }

    public static function forMappingTarget(Type $parameterType, string $parameterName)
    {
        return new ParameterBinding(
            $parameterType,
            $parameterName,
            false,
            true,
            false,
            null
        );
    }

    public static function forMappingContext(Type $parameterType, string $parameterName)
    {
        return new ParameterBinding(
            $parameterType,
            $parameterName,
            false,
            false,
            true,
            null
        );
    }

    public static function forSourceRHS(Type $parameterType, string $parameterName, SourceRHS $sourceRHS): ParameterBinding
    {
        return new ParameterBinding(
            $parameterType,
            null,
            false,
            false,
            false,
            $sourceRHS
        );
    }

    public function getType(): Type
    {
        return $this->type;
    }

    /**
     * @param Parameter[] $parameters
     * @return ParameterBinding[]
     */
    public static function fromParameters(array $parameters): array
    {
        $bindings = [];
        foreach ($parameters as $parameter) {
            $bindings[] = self::fromParameter($parameter);
        }

        return $bindings;
    }

    public static function fromParameter(Parameter $parameter): ParameterBinding
    {
        return new ParameterBinding(
            $parameter->getType(),
            $parameter->getName(),
            $parameter->isMappingTarget(),
            $parameter->isTargetType(),
            $parameter->isMappingContext(),
            null
        );
    }

    public static function forMappingTargetBinding(Type $resultType): ParameterBinding
    {
        return new ParameterBinding($resultType, null, true, false, false, null);
    }

    public static function forTargetTypeBinding(Type $classTypeOf): ParameterBinding
    {
        return new ParameterBinding($classTypeOf, null, false, true, false, null);
    }

    public static function forSourceTypeBinding(Type $sourceType): ParameterBinding
    {
        return new ParameterBinding($sourceType, null, false, false, true, null);
    }

    public function getVariableName(): ?string
    {
        return $this->variableName;
    }

    public function isTargetType(): bool
    {
        return $this->targetType;
    }

    public function isMappingTarget(): bool
    {
        return $this->mappingTarget;
    }

    public function isMappingContext(): bool
    {
        return $this->mappingContext;
    }

    public function getSourceRHS(): ?SourceRHS
    {
        return $this->sourceRHS;
    }
}
