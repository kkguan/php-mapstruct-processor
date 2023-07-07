<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Processor;

use Kkguan\PHPMapstruct\Processor\Internal\Gem\BeanMappingGem;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Parameter;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\TypeFactory;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\BeanMappingOptions;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\MapperOptions;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\MappingOptions;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\ParameterProvidedMethods;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\ParameterProvidedMethodsBuilder;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\SourceMethod;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\SourceMethodBuilder;
use Kkguan\PHPMapstruct\Processor\Internal\Option\Options;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Executables;

/**
 * A {@link ModelElementProcessor} which retrieves a list of {@link SourceMethod}s
 * representing all the mapping methods of the given bean mapper type as well as
 * all referenced mapper methods declared by other mappers referenced by the
 * current mapper.
 */
class MethodRetrievalProcessor implements ModelElementProcessor
{
    private TypeFactory $typeFactory;

    private Options $options;

    public function process(ProcessorContext $context, \ReflectionClass $mapperTypeElement, $sourceModel)
    {
        $this->typeFactory = $context->getTypeFactory();
        $mapperOptions = MapperOptions::getInstanceOn($mapperTypeElement, $context->getOptions());
        $prototypeMethods = $this->retrievePrototypeMethods($mapperTypeElement, $mapperOptions);
        $this->options = $context->getOptions();
        return $this->retrieveMethods($mapperTypeElement, $mapperTypeElement, $mapperOptions, $prototypeMethods);
    }

    public function getPriority(): int
    {
        return 1;
    }

    /**
     * Mapper注解中config配置未支持
     */
    private function retrievePrototypeMethods(\ReflectionClass $mapperTypeElement, MapperOptions $mapperOptions): array
    {
        // TODO
        return [];
//        var_dump($mapperTypeElement->getShortName(), $mapperTypeElement->getNamespaceName());
//        $methods = [];
//        foreach ($mapperTypeElement->getMethods() as $method) {
//            $parameters = $this->typeFactory->getParameters($method);
//            $containsTargetTypeParameter = SourceMethod::containsTargetTypeParameter($parameters);
//            $sourceMethod = $this->getMethodRequiringImplementation(
//                $method,
//                $parameters,
//                $containsTargetTypeParameter,
//                $mapperOptions,
//                $mapperTypeElement
//            );
//        }
    }

    /**
     * @param Parameter[] $parameters
     * @param SourceMethod[] $prototypeMethods
     * @param mixed $mapperToImplement
     */
    private function getMethodRequiringImplementation(
        \ReflectionMethod $method,
        array $parameters,
        bool $containsTargetTypeParameter,
        MapperOptions $mapperOptions,
        array $prototypeMethods,
        \ReflectionClass $mapperToImplement,
    ) {
        $returnType = $this->typeFactory->getReturnType($method);
        $sourceParameters = Parameter::getSourceParameters($parameters);
        $contextParameters = Parameter::getContextParameters($parameters);
        $targetParameter = $this->extractTargetParameter($parameters);
        $resultType = $this->selectResultType($returnType, $targetParameter);

        $isValid = $this->checkParameterAndReturnType(
            $method,
            $sourceParameters,
            $targetParameter,
            $contextParameters,
            $resultType,
            $returnType,
            $containsTargetTypeParameter
        );

        if (! $isValid) {
            return null;
        }

        // TODO: 这里还有问题
        $contextProvidedMethods = $this->retrieveContextProvidedMethods($contextParameters, $mapperToImplement, $mapperOptions);

        $beanMappingOptions = BeanMappingOptions::getInstanceOn(
            beanMapping: BeanMappingGem::instanceOn($method),
            mapperOptions: $mapperOptions,
            method: $method,
            typeFactory: $this->typeFactory
        );
        $mappingOptions = $this->getMappings($method, $beanMappingOptions);
        // TODO
        // IterableMapping未实现

        // MapMapping未实现

        // EnumMapping未实现

        $sourceMethodBuilder = new SourceMethodBuilder();
        return $sourceMethodBuilder
            ->setExecutable($method)
            ->setParameters($parameters)
            ->setReturnType($returnType)
            ->setExceptionTypes([])
            ->setMapper($mapperOptions)
            ->setBeanMapping($beanMappingOptions)
            ->setMappingOptions($mappingOptions)
            ->setIterableMappingOptionss(null)
            ->setMapMappingOptions(null)
            ->setValueMappingOptionss([])
            ->setEnumMappingOptions(null)
            ->setSubclassMappings([])
            ->setSubclassValidator(null)
            ->setTypeFactory($this->typeFactory)
            ->setPrototypeMethods($prototypeMethods)
            ->setContextProvidedMethods($contextProvidedMethods)
            ->build();
    }

    /**
     * @param Parameter[] $parameters
     */
    private function extractTargetParameter(array $parameters): ?Parameter
    {
        foreach ($parameters as $parameter) {
            if ($parameter->isMappingTarget()) {
                return $parameter;
            }
        }

        return null;
    }

    private function selectResultType(?Type $returnType, ?Parameter $targetParameter): ?Type
    {
        if ($targetParameter != null) {
            return $targetParameter->getType();
        }

        return $returnType;
    }

    /**
     * @param Parameter[] $contextParameters
     */
    private function retrieveContextProvidedMethods(array $contextParameters, \ReflectionClass $mapperToImplement, MapperOptions $mapperOptions): ParameterProvidedMethods
    {
        $builder = new ParameterProvidedMethodsBuilder();
        foreach ($contextParameters as $contextParam) {
            if ($contextParam->getType()->isPrimitive() || $contextParam->getType()->isArrayType()) {
                continue;
            }

            $contextParamMethods = $this->retrieveMethods(
                $contextParam->getType()->getTypeElement(),
                $mapperToImplement,
                $mapperOptions,
                []
            );

            $contextProvidedMethods = [];
            foreach ($contextParamMethods as $contextParamMethod) {
                if ($contextParamMethod->isLifecycleCallbackMethod() || $contextParamMethod->isObjectFactory() || $contextParamMethod->isPresenceCheck()) {
                    $contextProvidedMethods[] = $contextParamMethod;
                }
            }

            $builder->addMethodsForParameter($contextParam, $contextProvidedMethods);
        }

        return $builder->build();
    }

    private function checkParameterAndReturnType(
        \ReflectionMethod $method,
        array $sourceParameters,
        ?Parameter $targetParameter,
        array $contextParameters,
        ?Type $resultType,
        ?Type $returnType,
        bool $containsTargetTypeParameter
    ): bool {
        if (empty($sourceParameters)) {
            return false;
        }

        if ($targetParameter != null
            && (count($sourceParameters) + count($contextParameters) + 1 != count($method->getParameters()))) {
            return false;
        }

        if ($resultType == null) {
            return false;
        }

        // TODO:待翻

        return true;
    }

    /**
     * parser functions.
     *
     * @param SourceMethod[] $prototypeMethods
     * @return SourceMethod[]
     *
     * TODO: \ReflectionClass|\ReflectionType $usedMapper 这个后续封装成一个 TypeElement
     */
    private function retrieveMethods(
        \ReflectionClass|\ReflectionType $usedMapper,
        \ReflectionClass $mapperToImplement,
        MapperOptions $mapperOptions,
        array $prototypeMethods
    ): array {
        $methods = [];
        foreach ($usedMapper->getMethods() as $executable) {
            $method = $this->getMethod(
                $usedMapper,
                $executable,
                $mapperToImplement,
                $mapperOptions,
                $prototypeMethods
            );

            if ($method != null) {
                $methods[] = $method;
            }
        }

        // Add all methods of used mappers in order to reference them in the aggregated model
        if ($usedMapper === $mapperToImplement) {
            foreach ($mapperOptions->uses() as $usedMapper);
            // TODO: 待翻
        }

        // TODO  mapper注解uses处理
        return $methods;
    }

    private function getMethod(
        \ReflectionClass $usedMapper,
        \ReflectionMethod $method,
        \ReflectionClass $mapperToImplement,
        MapperOptions $mapperOptions,
        array $prototypeMethods
    ) {
        $parameters = $this->typeFactory->getParameters($method);
        $returnType = $this->typeFactory->getReturnType($method);
        $methodType = $this->typeFactory->getMethodTypeByDeclaredType($usedMapper, $method);

        $methodRequiresImplementation = (bool) ($method->getModifiers() & \ReflectionMethod::IS_ABSTRACT);
        $containsTargetTypeParameter = SourceMethod::containsTargetTypeParameter($parameters);

        // add method with property mappings if an implementation needs to be generated
        if (($usedMapper === $mapperToImplement) && $methodRequiresImplementation) {
            return $this->getMethodRequiringImplementation(
                method: $method,
                parameters: $parameters,
                containsTargetTypeParameter: $containsTargetTypeParameter,
                mapperOptions: $mapperOptions,
                prototypeMethods: $prototypeMethods,
                mapperToImplement: $mapperToImplement
            );
        }
        // otherwise add reference to existing mapper method
        if (
            $this->isValidReferencedMethod($parameters)
            || $this->isValidFactoryMethod($method, $parameters, $returnType)
            || $this->isValidLifecycleCallbackMethod($method)
            || $this->isValidPresenceCheckMethod($method, $returnType)
        ) {
            return $this->getReferencedMethod(
                $usedMapper,
                $methodType,
                $method,
                $mapperToImplement,
                $parameters
            );
        }

        return null;
    }

    /**
     * @param array<Parameter> $parameters
     * @return SourceMethod
     */
    private function getReferencedMethod(\ReflectionClass $usedMapper, ?\ReflectionMethod $methodType, ?\ReflectionMethod $method, \ReflectionClass $mapperToImplement, array $parameters)
    {
        $returnType = $this->typeFactory->getReturnType($methodType);
        $exceptionTypes = $this->typeFactory->getExceptionTypes($methodType);
        // $usedMapperAsType 这里获取的是 as 的东西，但是目前没有支持
        $usedMapperAsType = null;
//        $usedMapperAsType = $this->typeFactory->getType($usedMapper);

        // TODO:$definingType 这里的 $definingType 应该是要获取到 className 作为类名的
//        $definingType = $this->typeFactory->getTypeByMethod($method);

        $builder = new SourceMethodBuilder();

        $builder->setDeclaringMapper($usedMapper->getName() === $mapperToImplement->getName() ? null : $usedMapperAsType)
            ->setDefiningType(null)
            ->setExecutable($method)
            ->setParameters($parameters)
            ->setReturnType($returnType)
            ->setExceptionTypes($exceptionTypes)
            ->setTypeFactory($this->typeFactory)
            ->setVerboseLogging($this->options->isVerbose())
            ->build();

        return new SourceMethod($builder, null);
    }

    private function isValidReferencedMethod(array $parameters): bool
    {
        return $this->isValidReferencedOrFactoryMethod(1, 1, $parameters);
    }

    private function isValidFactoryMethod(
        \ReflectionMethod $method,
        array $parameters,
        ?Type $returnType
    ): bool {
        return ! $this->isVoid($returnType)
            && (
                $this->isValidReferencedOrFactoryMethod(0, 0, $parameters)
                || $this->hasFactoryAnnotation($method)
            );
    }

    /**
     * @param array<Parameter> $parameters
     */
    private function isValidReferencedOrFactoryMethod(
        int $sourceParamCount,
        int $targetParamCount,
        array $parameters
    ): bool {
        $validSourceParameters = $targetParameters = $targetTypeParameters = 0;

        foreach ($parameters as $parameter) {
            if ($parameter->isMappingTarget()) {
                ++$targetParameters;
            } elseif ($parameter->isTargetType()) {
                ++$targetTypeParameters;
            } elseif (! $parameter->isMappingContext()) {
                ++$validSourceParameters;
            }
        }

        return $validSourceParameters == $sourceParamCount
            && $targetParameters <= $targetParamCount
            && $targetTypeParameters <= 1;
    }

    /**
     * @return MappingOptions[]
     */
    private function getMappings(\ReflectionMethod $method, BeanMappingOptions $beanMappingOptions): array
    {
        $options = [];
        foreach ($method->getAttributes() as $attribute) {
            if ($mappingOptions = MappingOptions::addInstance($attribute, $beanMappingOptions)) {
                $options[] = $mappingOptions;
            }
        }

        return $options;
    }

    private function isVoid(?Type $type): bool
    {
        return $type == null || $type->isVoid();
    }

    private function hasFactoryAnnotation(\ReflectionMethod $method): bool
    {
        // TODO: 现在没有 factory 注解
        return false;
    }

    private function isValidLifecycleCallbackMethod(\ReflectionMethod $method): bool
    {
        return Executables::isLifecycleCallbackMethod($method);
    }

    private function isValidPresenceCheckMethod(\ReflectionMethod $method, ?Type $returnType): bool
    {
        // TODO: 未实现
        return false;
    }

    private function hasConditionAnnotation(\ReflectionMethod $method): bool
    {
        return false;
    }
}
