<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Beanmapping\MappingReferences;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Beanmapping\SourceReference;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Beanmapping\SourceReferenceBuilderFromProperty;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Parameter;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\ParameterBinding;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\MappingOptions;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\SelectionParameters;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\SourceMethod;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor\Accessor;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor\AccessorType;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor\ParameterElementAccessor;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor\ReadAccessor;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Str;

class BeanMappingMethodBuilder
{
    protected MappingBuilderContext $ctx;

    private SourceMethod $method;

    private array $existingVariableNames = [];

    private array $targetProperties = [];

    private array $unprocessedTargetProperties = [];

    /** @var Accessor[] */
    private array $unprocessedConstructorProperties = [];

    /** @var Parameter[] */
    private array $unprocessedSourceParameters = [];

    /** @var ReadAccessor[] */
    private array $unprocessedSourceProperties = [];

    private array $missingIgnoredSourceProperties = [];

    private ?MappingReferences $mappingReferences = null;

    private array $unprocessedDefinedTargets = [];

    /** @var PropertyMapping[] */
    private array $propertyMappings = [];

    // TODO: 第一期没有
    private $returnTypeBuilder;

    private bool $hasFactoryMethod = false;

    public function build(): ?BeanMappingMethod
    {
        $beanMapping = $this->method->getOptions()->getBeanMapping();
        $selectionParameters = $beanMapping != null ? $beanMapping->getSelectionParameters() : null;

        /* the return type that needs to be constructed (new or factorized), so for instance: */
        /*  1) the return type of a non-update method */
        /*  2) or the implementation type that needs to be used when the return type is abstract */
        /*  3) or the builder whenever the return type is immutable */
        $returnTypeToConstruct = null;

        // determine which return type to construct
        $cannotConstructReturnType = false;

//        dump($this->method->isVoid());
//         if (! $this->method->isVoid()) {
//            $returnTypeImpl = null;
//            if ($this->isBuilderRequired()) {
//                // the userDefinedReturn type can also require a builder. That buildertype is already set
        // //                $returnTypeImpl = $this->returnTypeBuilder
//            }
//        }

        if ($cannotConstructReturnType) {
            // If the return type cannot be constructed then no need to try to create mappings
            return null;
        }

        /* the type that needs to be used in the mapping process as target */
        $resultTypeToMap = $this->method->getResultType(); // $returnTypeToConstruct == null ? $this->method->getResultType() : $returnTypeToConstruct;
        $this->existingVariableNames = $this->method->getParameterNames();

        // TODO: unsupported mapping strategy
        $cms = null;
        $accessors = $resultTypeToMap->getPropertyWriteAccessors($cms);
        $this->targetProperties = array_keys($accessors);
        $this->unprocessedTargetProperties = $accessors;

        if (! $this->method->isUpdateMethod() && ! $this->hasFactoryMethod) {    //  这个判断后续需要加上
            $constructorAccessor = $this->getConstructorAccessor($resultTypeToMap);
            if ($constructorAccessor != null) {
                $this->unprocessedConstructorProperties = $constructorAccessor->getConstructorAccessors();
            // factoryMethod未实现
            } else {
                $this->unprocessedConstructorProperties = [];
            }

            $this->targetProperties = array_merge($this->targetProperties, array_keys($this->unprocessedConstructorProperties));
            $this->unprocessedTargetProperties = array_merge($this->unprocessedTargetProperties, $this->unprocessedConstructorProperties);
        } else {
            $this->unprocessedConstructorProperties = [];
        }

        foreach ($this->method->getSourceParameters() as $sourceParameter) {
            $this->unprocessedSourceParameters[] = $sourceParameter;
            if ($sourceParameter->getType()->isPrimitive()) {
                continue;
            }

            $readAccessors = $sourceParameter->getType()->getPropertyReadAccessors();
            foreach ($readAccessors as $key => $readAccessor) {
                $this->unprocessedSourceProperties[$key] = $readAccessor;
            }
        }

        if ($beanMapping != null) {
            // 未实现beanMapping
        }

        $this->initializeMappingReferencesIfNeeded($resultTypeToMap);

        $mappingErrorOccurred = $this->handleDefinedMappings($resultTypeToMap);
        if ($mappingErrorOccurred) {
            return null;
        }

        if (! $this->mappingReferences->isRestrictToDefinedMappings()) {
            $this->applyTargetThisMapping();
            // 映射未被Mapping注解的属性
            $this->applyPropertyNameBasedMapping();

            $this->applyParameterNameBasedMapping();
        }

        $this->handleUnprocessedDefinedTargets();

        $this->handleUnmappedConstructorProperties();

        // 部分特性未支持

        return new BeanMappingMethod(
            method: $this->method,
            existingVariableNames: $this->existingVariableNames,
            propertyMappings: $this->propertyMappings,
            factoryMethod: null,
            mapNullToDefault: false,
            returnTypeToConstruct: $returnTypeToConstruct,
            returnTypeBuilder: null,
            beforeMappingReferences: [],
            afterMappingReferences: [],
            finalizerMethod: null,
            mappingReferences: $this->mappingReferences,
            subclassMappings: []
        );
    }

    public function sourceMethod(SourceMethod $sourceMethod): static
    {
        $this->method = $sourceMethod;
        return $this;
    }

    public function mappingContext(MappingBuilderContext $mappingContext)
    {
        $this->ctx = $mappingContext;
        return $this;
    }

    public function returnTypeBuilder(mixed $returnTypeBuilder): static
    {
        $this->returnTypeBuilder = $returnTypeBuilder;
        return $this;
    }

    private function initializeMappingReferencesIfNeeded(Common\Type $resultTypeToMap): void
    {
        if ($this->mappingReferences === null && $this->method instanceof SourceMethod) {
            $readAndWriteTargetProperties = array_keys($this->unprocessedTargetProperties);
            $readAndWriteTargetProperties = array_unique(array_merge($readAndWriteTargetProperties, array_keys($resultTypeToMap->getPropertyReadAccessors())));
            $this->mappingReferences = MappingReferences::forSourceMethod(
                sourceMethod: $this->method,
                targetType: $resultTypeToMap,
                targetProperties: $readAndWriteTargetProperties,
                typeFactory: $this->ctx->getTypeFactory(),
            );
        }
    }

    private function handleDefinedMappings(Common\Type $resultTypeToMap): bool
    {
        $errorOccurred = false;
        $handledTargets = [];

        // first we have to handle nested target mappings
        if ($this->mappingReferences->hasNestedTargetReferences()) {
            // 目前不支持
            $errorOccurred = $this->handleDefinedNestedTargetMapping($handledTargets, $resultTypeToMap);
        }

        foreach ($this->mappingReferences->getMappingReferences() as $mapping) {
            if ($mapping->isValid()) {
                $target = $mapping->getTargetReference()->getShallowestPropertyName();
                if (! in_array($target, $handledTargets)) {
                    if ($this->handleDefinedMapping($mapping, $resultTypeToMap, $handledTargets)) {
                        $errorOccurred = true;
                    }
                }

                if ($mapping->getSourceReference() != null) {
                    $source = $mapping->getSourceReference()->getShallowestPropertyName();
                    if ($source != null) {
                        unset($this->unprocessedSourceProperties[$source]);
                    }
                }
            }
        }

        foreach ($handledTargets as $handledTarget) {
            if (isset($this->unprocessedTargetProperties[$handledTarget])) {
                unset($this->unprocessedTargetProperties[$handledTarget]);
            }
            if (isset($this->unprocessedConstructorProperties[$handledTarget])) {
                unset($this->unprocessedConstructorProperties[$handledTarget]);
            }
            if (isset($this->unprocessedDefinedTargets[$handledTarget])) {
                unset($this->unprocessedDefinedTargets[$handledTarget]);
            }
        }

        return $errorOccurred;
    }

    private function handleDefinedMapping(Beanmapping\MappingReference $mappingRef, Common\Type $resultTypeToMap, array &$handledTargets): bool
    {
        $errorOccured = false;
        $propertyMapping = null;
        $targetRef = $mappingRef->getTargetReference();
        /** @var MappingOptions $mapping */
        $mapping = $mappingRef->getMapping();
        // 未开发
        // unknown properties given via dependsOn()?
//        foreach ($mapping->getDependsOn() as $dependency);

        $targetPropertyName = $targetRef->getPropertyEntries()[0];

        // check if source / expression / constant are not somehow handled already
        if (in_array($targetPropertyName, $this->unprocessedDefinedTargets)) {
            return false;
        }

//        dump($this->unprocessedTargetProperties);
        /** @var ?Accessor $targetWriteAccessor */
        $targetWriteAccessor = $this->unprocessedTargetProperties[$targetPropertyName] ?? null;
        $targetReadAccessor = $resultTypeToMap->getReadAccessor($targetPropertyName);

        if ($targetWriteAccessor === null) {
            if ($targetReadAccessor === null) {
                // TODO
                $inheritContext = $mapping->getInheritContext();

                if ($inheritContext != null) {
                }

                $readAccessors = $resultTypeToMap->getPropertyReadAccessors();
                return true;
            }
            if ($mapping->getInheritContext() != null && $mapping->getInheritContext()->isReversed()) {
                // read only reversed mappings are implicitly ignored
                return false;
            }
            if (! $mapping->isIgnored()) {
                // report an error for read only mappings

                $msg = $args = null;

                if ($targetPropertyName === $mapping->getTargetName()) {
                    $msg = 'No write accessor found for property "%s" in result type "%s".';
                    $args = [$targetPropertyName, $resultTypeToMap->getFullyQualifiedName()];
                } else {
                    $msg = 'No write accessor found for property "%s" in result type "%s" (mapped from "%s").';
                    $args = [$targetPropertyName, $resultTypeToMap->getFullyQualifiedName(), $mapping->getTargetName()];
                }
                $this->ctx->getMessager()->info(sprintf($msg, ...$args));
                return true;
            }
            // TODO:待实现
        }

        // check the mapping options
        // its an ignored property mapping
        if ($mapping->isIgnored()) {
            // 如果需要忽略的话
//            dd($targetWriteAccessor, $targetWriteAccessor->getAccessorType());
//            if ($targetWriteAccessor != null && $targetWriteAccessor->getAccessorType() === AccessorType::PARAMETER) {
//                // Even though the property is ignored this is a constructor parameter.
//                // Therefore we have to initialize it
//                $accessedType = $this->ctx->getTypeFactory()->getType($targetWriteAccessor->getAccessorType());
//                $propertyMapping = (new PHPExpressionMappingBuilder())
//                    ->mappingContext($this->ctx)
//                    ->sourceMethod($this->method)
//                    ->phpExpression($accessedType->getNull())
//                    ->existingVariableNames($this->existingVariableNames)
//                    ->target($targetPropertyName, $targetReadAccessor, $targetWriteAccessor)
//                    ->dependsOn([])
//                    ->mirror(null)
//                    ->build();
//            }

//            $this->unprocessedTargetProperties

            if (in_array(Str::camel($mapping->getTargetName()), array_keys($this->unprocessedTargetProperties))) {
                // TODO: 兼容下划线和不带下划线的参数,在忽略的时候，将驼峰的参数也忽略掉
                unset($this->unprocessedTargetProperties[Str::camel($mapping->getTargetName())]);
            }

            $handledTargets[] = $targetPropertyName;
        }
        // its an expression
        // if we have an unprocessed target that means that it most probably is nested and we should
        // not generated any mapping for it now. Eventually it will be done though
//        else if ($mapping->getPHPExpression() != null) {
//            $propertyMapping = (new PHPExpressionMappingBuilder())
//                ->mappingContext($this->ctx)
//                ->sourceMethod($this->method)
//                ->phpExpression($mapping->getPHPExpression())
//                ->existingVariableNames($this->existingVariableNames)
//                ->target($targetPropertyName, $targetReadAccessor, $targetWriteAccessor)
//                ->dependsOn($mapping->getDependsOn())
//                ->mirror($mapping->getMirror())
//                ->build();
//        }
        else {
            // its a plain-old property mapping
            $sourceRef = $mappingRef->getSourceReference();

            if ($sourceRef === null) {
                foreach ($this->method->getSourceParameters() as $sourceParameter) {
                    $matchingSourceRef = $this->getSourceRefByTargetName(
                        $sourceParameter,
                        $targetPropertyName
                    );

                    if ($matchingSourceRef !== null) {
                        if ($sourceRef != null) {
                            $errorOccured = true;
                            break;
                        }

                        $sourceRef = $matchingSourceRef;
                    }
                }
            }

            if ($sourceRef === null) {
                foreach ($this->method->getSourceParameters() as $sourceParameter) {
                    if ($sourceParameter->getName() != $targetPropertyName) {
                        continue;
                    }
                    $sourceRef = (new SourceReferenceBuilderFromProperty())->sourceParameter($sourceParameter)->name($targetPropertyName)->build();
                    break;
                }
            }

            if ($sourceRef != null) {
                if ($sourceRef->isValid()) {
                    $propertyMapping = (new PropertyMappingBuilder())
                        ->mappingContext($this->ctx)
                        ->sourceMethod($this->method)
                        ->target($targetPropertyName, $targetReadAccessor, $targetWriteAccessor)
                        ->sourcePropertyName($mapping->getSourceName())
                        ->sourceReference($sourceRef)
                        //                    ->selectionParameters(mapping . getSelectionParameters())
                        ->formattingParameters($mapping->getFormattingParameters())
                        ->existingVariableNames($this->existingVariableNames)
                        //                    ->dependsOn(mapping . $this->getDependsOn())
                        //                    ->defaultValue($mapping->getDefaultValue())
                        //                    ->defaultJavaExpression(mapping . getDefaultJavaExpression())
                        //                    ->conditionJavaExpression(mapping . getConditionJavaExpression())
                        //                    ->mirror(mapping . getMirror())
                        ->options($mapping)
                        ->build();

                    $handledTargets[] = $targetPropertyName;

                    if (in_array(Str::camel($mapping->getTargetName()), array_keys($this->unprocessedTargetProperties))) {
                        // TODO: 兼容下划线和不带下划线的参数,在忽略的时候，将驼峰的参数也忽略掉
                        unset($this->unprocessedTargetProperties[Str::camel($mapping->getTargetName())]);
                    }

                    unset($this->unprocessedSourceProperties[$sourceRef->getShallowestPropertyName()]);
                    foreach ($this->unprocessedSourceParameters as $key => $unprocessedSourceParameter) {
                        if ($unprocessedSourceParameter === $sourceRef->getParameter()) {
                            unset($this->unprocessedSourceParameters[$key]);
                            break;
                        }
                    }
                } else {
                    $errorOccured = true;
                }
            } else {
                $errorOccured = true;
            }
        }

        if ($propertyMapping != null) {
            $this->propertyMappings[] = $propertyMapping;
        }

        return $errorOccured;
    }

    private function getSourceRefByTargetName(Common\Parameter $sourceParameter, string $targetPropertyName): ?SourceReference
    {
        $sourceRef = null;
        if ($sourceParameter->getType()->isPrimitive() || $sourceParameter->getType()->isArrayType()) {
            return $sourceRef;
        }

        $sourceReadAccessor = $sourceParameter->getType()->getReadAccessor($targetPropertyName);

        if ($sourceReadAccessor !== null) {
            $sourcePresenceChecker = $sourceParameter->getType()->getPresenceChecker($targetPropertyName);
            $declaredSourceType = new \ReflectionClass($sourceParameter->getType()->getFullyQualifiedName());
            $returnType = $this->ctx->getTypeFactory()->getReturnTypeByAccessor($declaredSourceType, $sourceReadAccessor);
            $sourceRef = (new SourceReferenceBuilderFromProperty())
                ->sourceParameter($sourceParameter)
                ->type($returnType)
                ->readAccessor($sourceReadAccessor)
                ->presenceChecker($sourcePresenceChecker)
                ->name($targetPropertyName)
                ->build();
        }

        return $sourceRef;
    }

    private function getDependsOn()
    {
    }

    /**
     * 获取构造函数访问器.
     * @throws \ReflectionException
     */
    private function getConstructorAccessor(Common\Type $type): ?ConstructorAccessor
    {
        if ($type->isAbstract()) {
            return null;
        }

        $reflectionClass = new \ReflectionClass($type->getFullyQualifiedName());
        $constructor = $reflectionClass->getConstructor();

        if (empty($constructor)) {
            return null;
        }

        if (! $constructor->isPublic()) {
            return null;
        }

        if (empty($constructor->getParameters())) {
            return null;
        }

        return $this->getConstructorAccessor2($type, $constructor);
    }

    private function getConstructorAccessor2(Common\Type $type, \ReflectionMethod $constructor): ConstructorAccessor
    {
        $constructorParameters = $this->ctx->getTypeFactory()->getParameters($constructor);

        $constructorAccessors = [];
        $parameterBindings = [];
        foreach ($constructorParameters as $constructorParameter) {
            $parameterName = $constructorParameter->getName();
            $parameterElement = $constructorParameter->getElement();
            $constructorAccessor = $this->createConstructorAccessor(
                $parameterElement,
                $parameterName,
            );
            $constructorAccessors[$parameterName] = $constructorAccessor;
            $parameterBindings[] = ParameterBinding::fromTypeAndName($constructorParameter->getType(), $constructorAccessor->getSimpleName());
        }

        return new ConstructorAccessor($parameterBindings, $constructorAccessors);
    }

    private function createConstructorAccessor(\ReflectionParameter $element, string $parameterName): ParameterElementAccessor
    {
        $this->existingVariableNames[] = $parameterName;
        return new ParameterElementAccessor($element, $parameterName);
    }

    private function applyTargetThisMapping(): void
    {
        $handledTargetProperties = [];
        foreach ($this->mappingReferences->getTargetThisReferences() as $targetThis);
        // TODO:

        // 未实现
    }

    private function applyPropertyNameBasedMapping(): void
    {
        $sourceReferences = [];
        foreach (array_keys($this->unprocessedTargetProperties) as $targetPropertyName) {
            foreach ($this->method->getSourceParameters() as $sourceParameter) {
                $sourceRef = $this->getSourceRefByTargetName($sourceParameter, $targetPropertyName);
                if ($sourceRef != null) {
                    $sourceReferences[] = $sourceRef;
                }
            }
        }
        $this->applyPropertyNameBasedMapping2($sourceReferences);
    }

    /**
     * @param SourceReference[] $sourceReferences
     */
    private function applyPropertyNameBasedMapping2(array $sourceReferences): void
    {
        foreach ($sourceReferences as $sourceRef) {
            $targetPropertyName = $sourceRef->getDeepestPropertyName();
            $targetPropertyWriteAccessor = $this->unprocessedTargetProperties[$targetPropertyName] ?? null;

            if ($targetPropertyWriteAccessor) {
                unset($this->unprocessedTargetProperties[$targetPropertyName]);
            }

            if (isset($this->unprocessedConstructorProperties[$targetPropertyName])) {
                unset($this->unprocessedConstructorProperties[$targetPropertyName]);
            }

            if ($targetPropertyWriteAccessor === null) {
                // 添加日志
                $this->ctx->getMessager()->info(sprintf('Several possible source properties for target property "%s".', $targetPropertyName));
                continue;
            }

            $targetPropertyReadAccessor = $this->method->getResultType()->getReadAccessor($targetPropertyName);
            if (empty($targetPropertyReadAccessor)) {
                $this->ctx->getMessager()->error(sprintf('No read accessor for target property "%s".', $targetPropertyName));
                continue;
            }
            $mappingRefs = $this->extractMappingReferences($targetPropertyName, false);
            $propertyMapping = (new PropertyMappingBuilder())
                ->mappingContext($this->ctx)
                ->sourceMethod($this->method)
                ->target($targetPropertyName, $targetPropertyReadAccessor, $targetPropertyWriteAccessor)
                ->sourceReference($sourceRef)
                ->existingVariableNames($this->existingVariableNames)
                ->forgeMethodWithMappingReferences($mappingRefs)
                ->options($this->method->getOptions()->getBeanMapping())
                ->build();

            foreach ($this->unprocessedSourceParameters as $key => $unprocessedSourceParameter) {
                if ($unprocessedSourceParameter === $sourceRef->getParameter()) {
                    unset($this->unprocessedSourceParameters[$key]);
                    break;
                }
            }
            if ($propertyMapping != null) {
                $this->propertyMappings[] = $propertyMapping;
            }
            unset($this->unprocessedDefinedTargets[$targetPropertyName], $this->unprocessedSourceProperties[$targetPropertyName]);
        }
    }

    private function extractMappingReferences(?string $targetProperty, bool $restrictToDefinedMappings): ?MappingReferences
    {
        if (isset($this->unprocessedDefinedTargets[$targetProperty])) {
            $mappings = $this->unprocessedDefinedTargets[$targetProperty];
            return new MappingReferences($mappings, [], $restrictToDefinedMappings);
        }
        return null;
    }

    private function applyParameterNameBasedMapping(): void
    {
        foreach ($this->unprocessedTargetProperties as $tkey => $targetProperty) {
            foreach ($this->unprocessedSourceParameters as $skey => $sourceParameter) {
                if ($sourceParameter->getName() == $tkey) {
                    $sourceRef = (new SourceReferenceBuilderFromProperty())
                        ->sourceParameter($sourceParameter)
                        ->name($tkey)
                        ->build();
                    $targetPropertyReadAccessor = $this->method->getResultType()->getReadAccessor($tkey);
                    $mappingRefs = $this->extractMappingReferences($tkey, false);
                    $propertyMapping = (new PropertyMappingBuilder())
                        ->mappingContext($this->ctx)
                        ->sourceMethod($this->method)
                        ->target($tkey, $targetPropertyReadAccessor, $targetProperty)
                        ->sourceReference($sourceRef)
                        ->existingVariableNames($this->existingVariableNames)
                        ->forgeMethodWithMappingReferences($mappingRefs)
                        ->options($this->method->getOptions()->getBeanMapping())
                        ->build();
                    $this->propertyMappings[] = $propertyMapping;
                    unset($this->unprocessedDefinedTargets[$tkey], $this->unprocessedSourceProperties[$tkey], $this->unprocessedSourceParameters[$skey],$this->unprocessedTargetProperties[$tkey]);

                    if (! $sourceParameter->getType()->isPrimitive()) {
                        $readAccessors = $sourceParameter->getType()->getPropertyReadAccessors();
                        foreach (array_keys($readAccessors) as $sourceProperty) {
                            unset($this->unprocessedSourceProperties[$sourceProperty]);
                        }
                    }

                    unset($this->unprocessedConstructorProperties[$tkey]);
                }
            }
        }
    }

    private function handleUnprocessedDefinedTargets(): void
    {
        // TODO
    }

    private function handleUnmappedConstructorProperties(): void
    {
        // TODO
//        foreach ($this->unprocessedConstructorProperties as $targetPropertyName => $entry) {
//            $accessedType = $this->ctx->getTypeFactory()->getType($entry->getAccessedType());
//            dump($accessedType);
//        }
    }

    /**
     * @return bool builder is required when there is a returnTypeBuilder and the mapping method is not update method.
     *              However, builder is also required when there is a returnTypeBuilder, the mapping target is the builder and
     *              builder is not assignable to the return type (so without building).
     */
    private function isBuilderRequired(): bool
    {
        return $this->returnTypeBuilder != null && (! $this->method->isUpdateMethod() || ! $this->method->isMappingTargetAssignableToReturnType());
    }

    /**
     * Find a factory method for a return type or for a builder.
     */
    private function initializeFactoryMethod(Type $returnTypeImpl, SelectionParameters $selectionParameters): void
    {
        $matchingFactoryMethods = ObjectFactoryMethodResolver::getMatchingFactoryMethods($this->method, $returnTypeImpl, $selectionParameters, $this->ctx);

        if (empty($matchingFactoryMethods)) {
            $this->hasFactoryMethod = true;
        } elseif (count($matchingFactoryMethods) === 1) {
            $this->hasFactoryMethod = true;
        } else {
            $this->hasFactoryMethod = true;
        }
    }
}
