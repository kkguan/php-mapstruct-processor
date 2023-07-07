<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Processor\Creation;

use Kkguan\PHPMapstruct\Processor\Internal\Conversion\Conversions;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Assignment;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\DefaultConversionContext;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\FormattingParameters;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\SourceRHS;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Model\ForgedMethodHistory;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Method;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Selector\MethodSelectors;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Selector\SelectedMethod;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Selector\SelectionCriteria;

class ResolvingAttempt
{
    private SelectionCriteria $selectionCriteria;

    /** @var Method[] */
    private array $methods;

    private Method $mappingMethod;

    private SourceRHS $sourceRHS;

    private MappingResolver $mappingResolver;

    private Conversions $conversions;

    public function __construct(
        array $sourceModel,
        Method $mappingMethod,
        ForgedMethodHistory $description,
        $formattingParameters,
        SourceRHS $sourceRHS,
        SelectionCriteria $criteria,
        $positionHint,
        $forger,
        array $builtIns,
        MethodSelectors $methodSelectors,
        MappingResolver $mappingResolver,
    ) {
        $this->mappingMethod = $mappingMethod;
        $this->description = $description;
        $this->methods = $this->filterPossibleCandidateMethods($sourceModel);
        $this->formattingParameters =
            $formattingParameters == null ? FormattingParameters::empty() : $formattingParameters;
        $this->sourceRHS = $sourceRHS;
        $this->supportingMethodCandidates = [];
        $this->selectionCriteria = $criteria;
        $this->positionHint = $positionHint;
        $this->forger = $forger;
        $this->builtIns = $builtIns;
        $this->methodSelectors = $methodSelectors;
        $this->mappingResolver = $mappingResolver;

        $this->conversions = new Conversions($this->mappingResolver->getTypeFactory());
    }

    public function getTargetAssignment(Type $sourceType, Type $targetType): ?Assignment
    {
        /** @var Assignment $assignment */
        $assignment = null;

        // first simple mapping method
        if ($this->allowMappingMethod()) {
            $matches = $this->getBestMatch($this->methods, $sourceType, $targetType);
            $this->reportErrorWhenAmbiguous($matches, $targetType);
            if (! empty($matches)) {
                $assignment = $this->mappingResolver->toMethodRef($matches[0]);
                $assignment->setAssignment($assignment);
                return $assignment;
            }
        }

        // then direct assignable
        if (! $this->hasQualfiers()) {
            if (
                ($sourceType->isAssignableTo($targetType)
                || $this->isAssignableThroughCollectionCopyConstructor($sourceType, $targetType))
                && $this->allowDirect($sourceType, $targetType)
            ) {
                return $this->sourceRHS;
            }
        }

        // At this point the SourceType will either
        // 1. be a String
        // 2. or when its a primitive / wrapped type and analysis successful equal to its TargetType. But in that
        //    case it should have been direct assignable.
        // In case of 1. and the target type is still a wrapped or primitive type we must assume that the check
        // in NativeType is not successful. We don't want to go through type conversion, double mappings etc.
        // with something that we already know to be wrong.
        if ($sourceType->isLiteral()) {
            // TODO
        }

        // then type conversion
        if ($this->allowConversion()) {
            if (! $this->hasQualfiers()) {
            }
            // TODO

            // check for a built-in method
        }

        if ($this->allow2Steps()) {
            // 2 step method, first: method(method(source))
            // TODO:PHP没有多态，理论上可以不管
//            $assignment = MethodMethod::getBestMatch($this, $sourceType, $targetType);
            // 2 step method, then: method(conversion(source))
//            $assignment = ConversionMethod::getBestMatch($this, $sourceType, $targetType);
            // stop here when looking for update methods.

            // 2 step method, finally: conversion(method(source))

            // php 不同内置类型强转实现
            $assignment = ConversionType::getBestMatch($this, $sourceType, $targetType);
            if (! empty($assignment)) {
                return $assignment;
            }
//            $assignment = $this->conversions->getTypeConversionAssignment($this->sourceRHS, $sourceType, $targetType);
        }

        if ($this->hasQualfiers()) {
            if ($sourceType->isCollectionType() || $sourceType->isArrayType()) {
                // Allow forging iterable mapping when no iterable mapping already found
                return $this->forger->get();
            }
        // TODO
        // 打印错误信息
        } elseif ($this->allowMappingMethod()) {
            // Only forge if we would allow mapping method
            // TODO: 这里的 forger 是还未实现完成的
            return $this->forger?->get();
        }

        // if nothing works, alas, the result is null
        return null;
    }

    public function hasQualfiers(): bool
    {
        return $this->selectionCriteria != null && $this->selectionCriteria->hasQualfiers();
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function toMethodRef(SelectedMethod $selectedMethod)
    {
        return $this->mappingResolver->toMethodRef($selectedMethod);
    }

    public function getSelectionCriteria(): SelectionCriteria
    {
        return $this->selectionCriteria;
    }

    public function getMappingMethod(): Method
    {
        return $this->mappingMethod;
    }

    public function getSourceRHS(): SourceRHS
    {
        return $this->sourceRHS;
    }

    public function getMappingResolver(): MappingResolver
    {
        return $this->mappingResolver;
    }

    public function resolveViaConversion(Type $sourceType, Type $targetType): ?ConversionAssignment
    {
        $conversionProvider = $this->conversions->getConversion($sourceType, $targetType);

        if (empty($conversionProvider)) {
            return null;
        }

        $ctx = new DefaultConversionContext(
            $this->getMappingResolver()->getTypeFactory(),
            $this->getMappingResolver()->getMessager(),
            $sourceType,
            $targetType,
            $this->formattingParameters
        );

//        $allUsedFields = $this->getMappingResolver()->getMapperReferences();
        // TODO: 私有方法暂时未支持

        $conversion = $conversionProvider->to($ctx);
        if (! empty($conversion)) {
            return new ConversionAssignment($sourceType, $targetType, $conversionProvider->to($ctx));
        }
        return null;
    }

    private function allowConversion(): bool
    {
        return $this->selectionCriteria != null && $this->selectionCriteria->isAllowConversion();
    }

    private function allow2Steps(): bool
    {
        return $this->selectionCriteria != null && $this->selectionCriteria->isAllow2Steps();
    }

    private function allowDirect(Type $sourceType, Type $targetType)
    {
        if ($this->selectionCriteria != null && $this->selectionCriteria->isAllowDirect()) {
            return true;
        }

        return $this->allowDirectByType($sourceType) || $this->allowDirectByType($targetType);
    }

    private function allowDirectByType(Type $type)
    {
        if ($type->isPrimitive()) {
            return true;
        }

        if ($type->isEnumType()) {
            return true;
        }

        if ($type->isArrayType()) {
            return $type->isPHPLangType() || $type->getComponentType()->isPrimitive();
        }

        if ($type->isIterableOrStreamType()) {
            // TODO: 不支持
        }

        if ($type->isMapType()) {
            // TODO: 不支持
        }

        return $type->isPHPLangType();
    }

    private function allowMappingMethod(): bool
    {
        return $this->selectionCriteria != null && $this->selectionCriteria->isAllowMappingMethod();
    }

    private function filterPossibleCandidateMethods(array $candidateMethods): array
    {
        $result = [];
        foreach ($candidateMethods as $candidate) {
            if ($this->isCandidateForMapping($candidate)) {
                $result[] = $candidate;
            }
        }

        return $result;
    }

    /**
     * Whether the given source and target type are both a collection type or both a map type and the
     * source value can be propagated via a copy constructor.
     */
    private function isAssignableThroughCollectionCopyConstructor(Type $sourceType, Type $targetType): bool
    {
        $bothCollectionOrMap = false;
        return false;
//        if ($sourceType->isCollectionType())
    }

    private function isCandidateForMapping(Method $methodCandidate): bool
    {
        return $this->isCreateMethodForMapping($methodCandidate) || $this->isUpdateMethodForMapping($methodCandidate);
    }

    private function isCreateMethodForMapping(Method $methodCandidate): bool
    {
        return count($methodCandidate->getSourceParameters()) == 1
            && ! ($methodCandidate->getReturnType() == null)
            && $methodCandidate->getMappingTargetParameter() == null
            && ! $methodCandidate->isLifecycleCallbackMethod();
    }

    private function isUpdateMethodForMapping(Method $methodCandidate): bool
    {
        return count($methodCandidate->getSourceParameters()) == 1
            && $methodCandidate->getMappingTargetParameter() != null
            && ! $methodCandidate->isLifecycleCallbackMethod();
    }

    /**
     * @param Method[] $methods
     */
    private function getBestMatch(array $methods, Type $source, Type $target): ?array
    {
        return $this->methodSelectors->getMatchingMethods(
            $this->mappingMethod,
            $methods,
            $source,
            $target,
            $target,
            $this->selectionCriteria
        );
    }

    private function reportErrorWhenAmbiguous(array $methods, Type $targetType)
    {
        if (count($methods) > 1) {
            $this->reportError(
                $this->mappingMethod,
                $methods,
                $targetType,
                'Ambiguous mapping methods found for mapping property "%s" to "%s": %s.'
            );
        }
    }

    private function reportError($mappingMethod, $methods, $targetType, $message)
    {
        $methodNames = [];
        foreach ($methods as $method) {
            $methodNames[] = $method->getDeclaringMapper()->getMapperClassName() . '::' . $method->getName();
        }

        // TODO: 回头这里改成 psr/logger 日志
        echo sprintf(
            $message,
            $mappingMethod->getPropertyName(),
            $targetType,
            implode(', ', $methodNames)
        );
    }
}
