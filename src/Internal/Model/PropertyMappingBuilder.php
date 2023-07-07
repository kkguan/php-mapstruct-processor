<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model;

use Kkguan\PHPMapstruct\Processor\Internal\Gem\NullValuePropertyMappingStrategyGem;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Assignment\SetterWrapper;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Beanmapping\MappingReferences;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Assignment;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\FormattingParameters;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\SourceRHS;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\ConstantMappingBuilder;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\DelegatingOptions;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Method;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\SelectionParameters;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Selector\SelectionCriteria;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor\Accessor;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor\AccessorType;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor\ReadAccessor;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Str;

class PropertyMappingBuilder extends AbstractBaseBuilder
{
    private string $targetPropertyName;

    private ReadAccessor $targetReadAccessor;

    private Accessor $targetWriteAccessor;

    private Common\Type $targetType;

    private string $targetWriteAccessorType;

    private string $sourcePropertyName;

    private Beanmapping\SourceReference $sourceReference;

    private ?SourceRHS $rightHandSide;

    private ?MappingReferences $forgeMethodWithMappingReferences = null;

    private bool $forgedNamedBased = true;

    private ?FormattingParameters $formattingParameters = null;

    private $nvpms;

    private ?string $defaultValue = null;

    private ?string $defaultPHPExpression = null;

    public function __construct()
    {
    }

    public function sourceMethod(Method $method)
    {
        return parent::method($method);
    }

    public function target(string $targetPropertyName, ReadAccessor $targetReadAccessor, Accessor $targetWriteAccessor)
    {
        $this->targetPropertyName = $targetPropertyName;
        $this->targetReadAccessor = $targetReadAccessor;
        $this->targetWriteAccessor = $targetWriteAccessor;

        $accessedType = $targetWriteAccessor->getAccessedType();
        if ($accessedType instanceof \ReflectionParameter) {
            $this->targetType = $this->ctx->getTypeFactory()->getType($accessedType->getType());
        } elseif ($accessedType instanceof \ReflectionType) {
            $this->targetType = $this->ctx->getTypeFactory()->getType($accessedType);
        }
        $this->targetWriteAccessorType = $targetWriteAccessor->getAccessorType();

        return $this;
    }

    public function sourcePropertyName(string $sourcePropertyName)
    {
        $this->sourcePropertyName = $sourcePropertyName;
        return $this;
    }

    public function sourceReference(Beanmapping\SourceReference $sourceReference)
    {
        $this->sourceReference = $sourceReference;
        return $this;
    }

    public function getDefaultValue(): ?string
    {
        return $this->defaultValue;
    }

    public function setDefaultValue(?string $defaultValue): PropertyMappingBuilder
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    public function options(DelegatingOptions $mapping)
    {
        // TODO: 特性未完成
//        $this->mappingController = $mapping->getMappingControl();
//        $this->nvpms = $mapping->getNullValueCheckStrategy();
//        if ($this->method->isUpdateMethod()) {
//            $this->nvpms = $mapping->getNullValuePropertyMappingStrategy();
//        }
        return $this;
    }

    public function build(): PropertyMapping
    {
        $this->rightHandSide = $this->getSourceRHS($this->sourceReference);
        $this->rightHandSide->setUseElementAsSourceTypeForMatching(
            $this->targetWriteAccessorType == AccessorType::ADDER
        );

        if ($this->targetWriteAccessorType == AccessorType::ADDER) {
            $preferUpdateMethods = false;
        } else {
            $preferUpdateMethods = $this->method->getMappingTargetParameter() != null;
        }

        $criteria = SelectionCriteria::forMappingMethods(
            null,
            null,
            $this->targetPropertyName,
            $preferUpdateMethods
        );

        // forge a method instead of resolving one when there are mapping options.
        /** @var Assignment $assignment */
        $assignment = null;

        if ($this->forgeMethodWithMappingReferences == null) {
            $assignment = $this->ctx->getMappingResolver()->getTargetAssignment(
                mappingMethod: $this->method,
                description: $this->getForgedMethodHistory($this->rightHandSide),
                targetType: $this->targetType,
                formattingParameters: $this->formattingParameters,
                criteria: $criteria,
                sourceRHS: $this->rightHandSide, // TODO:这里未实现
                positionHint: null,
                forger: $this->forge()
            );

//            $assignment = $this->ctx->getMappingResolver()->getTargetAssignment(
//                mappingMethod: $this->method,
//                description: $this->getForgedMethodHistory($this->rightHandSide),
//                targetType: $this->targetType,
//                formattingParameters: null,
//                criteria: $criteria,
//                sourceRHS: $this->rightHandSide,
//                positionHint: null,
//                forger: function () {$this->forge(); }
//            );
        } else {
            $assignment = $this->forge();
        }

        $sourceType = $this->rightHandSide->getSourceType();
        if ($assignment != null) {
            // debug info
            if ($this->ctx->getOptions()->isVerbose()) {
                $this->ctx->getMessager()->info(sprintf('selecting property mapping: %s.', $assignment));
            }
//            if ($this->targetWriteAccessor->getAccessorType() == AccessorType::SETTER || AccessorType::isFieldAssignment($this->targetWriteAccessor->getAccessorType())) {
//                // target accessor is setter, so decorate assignment as setter
//                if ($this->targetType->isCallingUpdateMethod()) {
//                } elseif ()
//            }
            if ($this->targetType->isCollectionOrMapType()) {
            } elseif ($this->targetType->isArrayType() && $sourceType->isArrayType() && $assignment->getType()->isDirect()) {
            } else {
                $assignment = $this->assignToPlain($this->targetType, $this->targetWriteAccessorType, $assignment);
            }
        } else {
            $this->reportCannotCreateMapping();
        }

        return new PropertyMapping(
            name: $this->targetPropertyName,
            sourceBeanName: $this->rightHandSide->getSourceParameterName(),
            targetWriteAccessorName: $this->targetWriteAccessor->getSimpleName(),
            targetReadAccessorProvider: $this->targetReadAccessor,
            targetType: $this->targetType,
            assignment: $assignment,
            dependsOn: [],
            defaultValueAssignment: $this->getDefaultValueAssignment($assignment),
            constructorMapping: $this->targetWriteAccessorType == AccessorType::PARAMETER
        );
    }

    public function formattingParameters(?FormattingParameters $formattingParameters): static
    {
        $this->formattingParameters = $formattingParameters;
        return $this;
    }

    public function forgeMethodWithMappingReferences(?MappingReferences $mappingReferences): static
    {
        $this->forgeMethodWithMappingReferences = $mappingReferences;
        return $this;
    }

    public function isFieldAssignment(): bool
    {
        return AccessorType::isFieldAssignment($this->targetWriteAccessorType);
    }

    public function forParameterMapping(string $name, Type $sourceType, Type $returnType, Method $baseOn): ForgedMethod
    {
        return new ForgedMethod(
            name: $name,
            sourceType: $sourceType,
            returnType: $returnType,
            additionalParameters: [],
            baseOn: $baseOn,
            history: null,
            mappingReferences: new MappingReferences([], [], false),
            forgedNameBased: false
        );
    }

    protected function canGenerateAutoSubMappingBetween(Type $sourceType, Type $targetType): bool
    {
        if ($sourceType->isPrimitive() || $targetType->isPrimitive()) {
            return false;
        }

        if ($sourceType->isIterableOrStreamType() || $targetType->isIterableOrStreamType()) {
            return false;
        }

        if ($sourceType->isMapType() || $targetType->isMapType()) {
            return false;
        }

        if ($sourceType->isCollectionType() || $targetType->isCollectionType()) {
            return false;
        }

        if ($sourceType->isArrayType() || $targetType->isArrayType()) {
            return false;
        }

        return true;
    }

    private function getDefaultValueAssignment(?Assignment $rhs): ?Assignment
    {
        if ($this->defaultValue != null && (! $rhs->getSourceType()->isPrimitive() || $rhs->getSourcePresenceCheckerReference() != null)) {
            // cannot check on null source if source is primitive unless it has a presence checker
            // TODO
            $build = (new ConstantMappingBuilder())
                ->setConstantExpression($this->defaultValue)
                ->setFormattingParameters($this->formattingParameters)
                ->setSelectionParameters($this->selectionParameters)
                ->dependsOn(null)
                ->existingVariableNames($this->existingVariableNames)
                ->mappingContext($this->ctx)
                ->sourceMethod($this->method)
                ->target($this->targetPropertyName, $this->targetReadAccessor, $this->targetWriteAccessor)
                ->build();

            return $build->getAssignment();
        }

        if ($this->defaultPHPExpression != null && (! $rhs->getSourceType()->isPrimitive() || $rhs->getSourcePresenceCheckerReference() != null)) {
        }

        return null;
    }

    /**
     * Report that a mapping could not be created.
     */
    private function reportCannotCreateMapping()
    {
        $this->ctx->getMessager()->error(sprintf(
            'Cannot create mapping method: %s.%s().',
            $this->method->getDeclaringMapper()?->getName(),
            $this->method->getName()
        ));
    }

    private function getEnumAssignment()
    {
        // TODO: getEnumAssignment
    }

    private function getSourceRHS(Beanmapping\SourceReference $sourceReference)
    {
        $sourceParam = $sourceReference->getParameter();
        $propertyEntry = $sourceReference->getDeepestProperty();

        if ($propertyEntry == null) {
            $sourceRHS = new SourceRHS(
                sourceParameterName: $sourceParam->getName(),
                sourceReference: $sourceParam->getName(),
                sourcePresenceCheckerReference: null,
                sourceType: $sourceParam->getType(),
                existingVariableNames: $this->existingVariableNames,
                sourceErrorMessagePart: (string) $sourceReference
            );
            // TODO
//            $this->getSourcePresenceCheckerRef($sourceReference, $sourceRHS);
            $sourceRHS->setSourcePresenceCheckerReference(null);

            return $sourceRHS;
        }
        if (! $sourceReference->isNested()) {
            $sourceRef = sprintf('$%s->%s', $sourceParam->getName(), $propertyEntry->getReadAccessor()->getReadValueSource());
            $sourceRHS = new SourceRHS(
                sourceParameterName: $sourceParam->getName(),
                sourceReference: $sourceRef,
                sourcePresenceCheckerReference: null,
                sourceType: $propertyEntry->getType(),
                existingVariableNames: $this->existingVariableNames,
                sourceErrorMessagePart: (string) $sourceReference
            );
            // TODO
//            $this->getSourcePresenceCheckerRef($sourceReference, $sourceRHS);
            $sourceRHS->setSourcePresenceCheckerReference(null);

            return $sourceRHS;
        }
        // nested property given as dot path

        $sourceType = $propertyEntry->getType();

        if ($sourceType->isPrimitive() && ! $this->targetType->isPrimitive()) {
            // Handle null's. If the forged method needs to be mapped to an object, the forged method must be
            // able to return null. So in that case primitive types are mapped to their corresponding wrapped
            // type. The source type becomes the wrapped type in that case.
            $sourceType = $this->ctx->getTypeFactory()->getWrappedType($sourceType);
        }

        // TODO
        // forge a method from the parameter type to the last entry type.
        $forgedName = Str::joinAndCamelize($sourceReference->getElementNames());
        $forgedName = Str::getSafeVariableName($forgedName, $this->ctx->getReservedNames());
        $sourceParameterType = $sourceReference->getParameter()->getType();
        $methodRef = $this->forParameterMapping($forgedName, $sourceParameterType, $sourceType, $this->method);

        $builder = new NestedPropertyMappingMethodBuilder();
        $nestedPropertyMapping = $builder->setMethod($methodRef)
            ->setPropertyEntries($sourceReference->getPropertyEntries())
            ->setMappingContext($this->ctx)
            ->build();

        // add if not yet existing
        if ($this->ctx->getMappingsToGenerate() !== $nestedPropertyMapping) {
            $this->ctx->addMappingsToGenerate($nestedPropertyMapping);
        } else {
            $forgedName = $this->ctx->getExistingMappingMethod($nestedPropertyMapping)->getName();
        }

        $sourceRef = sprintf('$this->%s($%s)', $forgedName, $sourceParam->getName());
        $sourceRhs = new SourceRHS(
            sourceParameterName: $sourceParam->getName(),
            sourceReference: $sourceRef,
            sourcePresenceCheckerReference: null,
            sourceType: $sourceType,
            existingVariableNames: $this->existingVariableNames,
            sourceErrorMessagePart: (string) $sourceReference
        );
        $sourceRhs->setSourcePresenceCheckerReference($this->getSourcePresenceCheckerRef($sourceReference, $sourceRhs));

        // create a local variable to which forged method can be assigned.
        $desiredName = $propertyEntry->getName();
        $sourceRhs->setSourceLocalVarName($sourceRhs->createUniqueVarName($desiredName));
        // TODO:嵌套未实现
        return $sourceRhs;
    }

    private function getSourcePresenceCheckerRef(Beanmapping\SourceReference $sourceReference, SourceRHS $sourceRHS)
    {
        // expression未实现

        return null;
//        $selectionParameters = SelectionParameters::forSourceRHS($sourceRHS);
//        PresenceCheckMethodResolver::getPresenceCheck(
//            $this->method,
//            $selectionParameters,
//            $this->ctx
//        );
    }

    private function forge(): ?Assignment
    {
        $assignment = null;
        $sourceType = $this->rightHandSide->getSourceType();
        // TODO: 未实现原始中数组等其他类型

        // 如果是 Collection，或者是 array，并且是迭代器（PHP不需要迭代器)
//        if (($sourceType->isCollectionType() || $sourceType->isArrayType()) && $this->targetType->isIterableOrStreamType()) {
//            $assignment = $this->forgeIterableMapping();
//        }

        // TODO: 这里目前只实现数组的形式

        if ($sourceType->isArrayType() && $this->targetType->isArrayType()) {
            $assignment = $this->forgeArrayMapping($sourceType, $this->targetType, $sourceType);
        } else {
            $assignment = $this->forgeMapping($this->rightHandSide);
        }

        if (! empty($assignment)) {
        }

        return $assignment;
    }

    private function forgeArrayMapping(Type $sourceType, Type $targetType, SourceRHS $sourceRHS)
    {
        dd('还未实现');
        $sourceElementType = $sourceType->getTypeElement();
        $targetElementType = $targetType->getTypeElement();

//        $sourceRHS = $this->getSourceRHS($this->rightHandSide->getSourceReference());

        $sourceRHS->setSourcePresenceCheckerReference(null);

        $sourceRHS->setSourceReference($sourceRHS->getSourceReference() . '->' . $this->rightHandSide->getSourceReference()->getDeepestProperty()->getReadAccessor()->getReadValueSource());

        $sourceRHS->setSourceType($sourceElementType);

        $sourceRHS->setSourceErrorMessagePart($sourceRHS->getSourceErrorMessagePart() . '->' . $this->rightHandSide->getSourceReference()->getDeepestProperty()->getReadAccessor()->getReadValueSource());

        $assignment = $this->forgeMapping($sourceType, $targetType, $sourceRHS);

        $targetWriteAccessor = $this->targetWriteAccessor;

        $targetReadAccessor = $this->targetReadAccessor;

        $targetPropertyName = $this->targetPropertyName;

        $targetType = $this->targetType;

        $targetWriteAccessorType = $this->targetWriteAccessorType;

        $existingVariableNames = $this->existingVariableNames;

        $formattingParameters = $this->formattingParameters;

        $forgeMethodWithMappingReferences = $this->forgeMethodWithMappingReferences;

        $method = $this->method;

        $ctx = $this->ctx;

        $sourceRHS = $this->rightHandSide;

        $targetRHS = $this->rightHandSide;

        $targetReadAccessorProvider = $this->targetReadAccessor;

        $targetWriteAccessorName = $this->targetWriteAccessor->getSimpleName();

        $sourceBeanName = $this->rightHandSide->getSourceParameterName();

        $name = $this->targetPropertyName;

        $dependsOn = [];

        $constructorMapping = $this->targetWriteAccessorType == AccessorType::PARAMETER;

        $defaultValueAssignment = null;

        return new ArrayMapping(
            name: $name,
            sourceBeanName: $sourceBeanName,
            targetWriteAccessorName: $targetWriteAccessorName,
            targetReadAccessorProvider: $targetReadAccessorProvider,
            targetType: $targetType,
            assignment: $assignment,
            dependsOn: $dependsOn,
            defaultValueAssignment: $defaultValueAssignment,
            constructorMapping: $constructor
        );
    }

    private function forgeMapping(SourceRHS $sourceRHS)
    {
        if ($this->targetWriteAccessorType == AccessorType::ADDER) {
            $sourceType = $sourceRHS->getSourceTypeForMatching();
        } else {
            $sourceType = $sourceRHS->getSourceType();
        }
        // TODO
        if ($this->forgedNamedBased && ! $this->canGenerateAutoSubMappingBetween($sourceType, $this->targetType)) {
            return null;
        }

        return $this->forgeMapping2($sourceType, $this->targetType, $sourceRHS);
    }

    private function forgeMapping2(Common\Type $sourceType, Common\Type $targetType, SourceRHS $sourceRHS)
    {
        // Fail fast. If we could not find the method by now, no need to try
        if ($sourceType->isPrimitive() || $targetType->isPrimitive()) {
            dump($sourceType, $targetType, 999);
            return null;
        }
    }

    private function getForgedMethodHistory(SourceRHS $sourceRHS): ForgedMethodHistory
    {
        return $this->getForgedMethodHistoryBySuffix($sourceRHS, '');
    }

    private function getForgedMethodHistoryBySuffix(SourceRHS $sourceRHS, string $suffix): ForgedMethodHistory
    {
        $history = null;
//        if ( method instanceof ForgedMethod ) {
//        ForgedMethod method = (ForgedMethod) this.method;
//                history = method.getHistory();
//            }

        return new ForgedMethodHistory(
            history: $history,
            sourceElement: $this->getSourceElementName() . $suffix,
            targetPropertyName: $this->targetPropertyName . $suffix,
            sourceType: $sourceRHS->getSourceType(),
            targetType: $this->targetType,
            usePropertyNames: true,
            elementType: 'property'
        );
    }

    private function getSourceElementName()
    {
        $sourceParam = $this->sourceReference->getParameter();
        $propertyEntries = $this->sourceReference->getPropertyEntries();
        if (empty($propertyEntries)) {
            return $sourceParam->getName();
        }
        if (count($propertyEntries) == 1) {
            $propertyEntry = $propertyEntries[0];
            return $propertyEntry->getName();
        }

        return implode('.', $this->sourceReference->getElementNames());
    }

    private function assignToPlain(Common\Type $targetType, string $targetAccessorType, Assignment $rightHandSide)
    {
        if ($targetAccessorType === AccessorType::SETTER || AccessorType::isFieldAssignment($targetType)) {
            $result = $this->assignToPlainViaSetter($targetType, $rightHandSide);
        } else {
            $result = $this->assignToPlainViaAdder($rightHandSide);
        }
        return $result;
    }

    private function assignToPlainViaSetter(Common\Type $targetType, Assignment $rhs): Assignment
    {
        if ($rhs->isCallingUpdateMethod()) {
            if ($targetType == null) {
                $this->ctx->getMessager()->info(sprintf('No read accessor found for property "%s" in target type.', $this->targetPropertyName));
            }

            // TODO
            $factory = ObjectFactoryMethodResolver::getFactoryMethod();
        } else {
            // If the property mapping has a default value assignment then we have to do a null value check
            $includeSourceNullCheck = $this->setterWrapperNeedsSourceNullCheck($rhs, $targetType);
            if (! $includeSourceNullCheck) {
                // solution for #834 introduced a local var and null check for nested properties always.
                // however, a local var is not needed if there's no need to check for null.
                $rhs->setSourceLocalVarName(null);
            }

            return new SetterWrapper(
                rhs: $rhs,
                thrownTypesToExclude: [],
                fieldAssignment: $this->isFieldAssignment(),
                includeSourceNullCheck: $includeSourceNullCheck,
                setExplicitlyToNull: false, // $includeSourceNullCheck && $this->nvpms == SET_TO_NULL && !targetType.isPrimitive()
                setExplicitlyToDefault: false, // nvpms == SET_TO_DEFAULT
            );
        }
    }

    private function assignToPlainViaAdder(Assignment $rightHandSide): Assignment
    {
    }

    private function setterWrapperNeedsSourceNullCheck(Assignment $rhs, Common\Type $targetType): bool
    {
        // TODO
        if ($rhs->getSourceType()->isPrimitive() && $rhs->getSourceReference() == null) {
            // If the source type is primitive or it doesn't have a presence checker then
            // we shouldn't do a null check
            return false;
        }

//        // TODO:
//        if ($this->nvpms == NullValuePropertyMappingStrategyGem::SET_TO_NULL && ! $targetType->isPrimitive()) {
//            // NullValueCheckStrategy is ALWAYS -> do a null check
//            return true;
//        }
//
//        if ($rhs->getSourcePresenceCheckerReference() != null) {
//            // There is an explicit source presence check method -> do a null / presence check
//            return true;
//        }
//
//        if ($this->nvpms == NullValuePropertyMappingStrategyGem::SET_TO_DEFAULT || $this->nvpms == NullValuePropertyMappingStrategyGem::IGNORE) {
//            // NullValuePropertyMapping is SET_TO_DEFAULT or IGNORE -> do a null check
//            return true;
//        }
//
//        if ($rhs->getType()->isConverted()) {
//            // A type conversion is applied, so a null check is required
//            return true;
//        }
//
//        if ($rhs->getType()->isDirect() && $targetType->isPrimitive()) {
//            // If the type is direct and the target type is primitive (i.e. we are unboxing) then check is needed
//            return true;
//        }
//
//        if ($this->defaultValue != null || $this->defaultJavaExpression != null) {
//            // If there is default value defined then a check is needed
//            return true;
//        }

        return false;
    }
}
