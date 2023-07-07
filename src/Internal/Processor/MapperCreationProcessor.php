<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Processor;

use Kkguan\PHPMapstruct\Processor\Internal\Model\BeanMappingMethod;
use Kkguan\PHPMapstruct\Processor\Internal\Model\BeanMappingMethodBuilder;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\TypeFactory;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Decorator;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Mapper;
use Kkguan\PHPMapstruct\Processor\Internal\Model\MappingBuilderContext;
use Kkguan\PHPMapstruct\Processor\Internal\Model\MappingMethod;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\MapperOptions;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\SourceMethod;
use Kkguan\PHPMapstruct\Processor\Internal\Option\Options;
use Kkguan\PHPMapstruct\Processor\Internal\Processor\Creation\MappingResolver;
use Kkguan\PHPMapstruct\Processor\Internal\Util\AccessorNamingUtils;

class MapperCreationProcessor implements ModelElementProcessor
{
    private TypeFactory $typeFactory;

    private Options $options;

    private AccessorNamingUtils $accessorNaming;

    private MappingBuilderContext $mappingContext;

    /**
     * @param SourceMethod[] $sourceModel
     */
    public function process(ProcessorContext $context, \ReflectionClass $mapperTypeElement, $sourceModel)
    {
        $this->typeFactory = $context->getTypeFactory();
        $this->options = $context->getOptions();
        $this->accessorNaming = $context->getAccessorNaming();

        $mapperOptions = MapperOptions::getInstanceOn($mapperTypeElement, $context->getOptions());
        // TODO: mapper 里面的 uses 参数配置暂时不实现
        $mapperReferences = $this->initReferencedMappers($mapperTypeElement, $mapperOptions);

        $ctx = new MappingBuilderContext(
            typeFactory: $this->typeFactory,
            accessorNaming: $this->accessorNaming,
            options: $this->options,
            mappingResolver: new MappingResolver(
                typeFactory: $this->typeFactory,
                sourceModel: $sourceModel,
                mapperReferences: $mapperReferences,
                verboseLogging: $this->options->isVerbose(),
                messager: $this->options->getLogger(),
            ),
            mapperTypeElement: $mapperTypeElement,
            sourceModel: $sourceModel,
            mapperReferences: $mapperReferences
        );
        $this->mappingContext = $ctx;
        return $this->getMapper($mapperTypeElement, $mapperOptions, $sourceModel);
    }

    public function getPriority(): int
    {
        return 1000;
    }

    /**
     * @param SupportingMappingMethod[] $supportingMappingMethods
     * @param Field[] $targets
     */
    public static function addAllFieldsIn(array $supportingMappingMethods, array $targets)
    {
        // TODO: mapper fields 还未实现
    }

    /**
     * @param SupportingMappingMethod[] $supportingMappingMethods
     * @param SupportingConstructorFragment[] $targets
     */
    public static function addAllFragmentsIn(array $supportingMappingMethods, array $targets)
    {
        // TODO：所有代码片段的处理
    }

    /**
     * @param SourceMethod[] $methods
     */
    private function getMapper(\ReflectionClass $mapperTypeElement, MapperOptions $mapperOptions, array $methods): Mapper
    {
        /** @var MappingMethod[] $mappingMethods */
        $mappingMethods = $this->getMappingMethods($mapperOptions, $methods);
        foreach ($this->mappingContext->getUsedSupportedMappings() as $value) {
            $mappingMethods[] = $value;
        }
        foreach ($this->mappingContext->getMappingsToGenerate() as $value) {
            $mappingMethods[] = $value;
        }

        $fields = $this->mappingContext->getMapperReferences();
        $supportingFieldSet = $this->mappingContext->getUsedSupportedFields();

        static::addAllFieldsIn($this->mappingContext->getUsedSupportedMappings(), $supportingFieldSet);

        foreach ($supportingFieldSet as $item) {
            $fields[] = $item;
        }

        // handler constructorfragments
        // TODO: 构造函数
        $constructorFragments = [];
        static::addAllFragmentsIn($this->mappingContext->getUsedSupportedMappings(), $constructorFragments);
        $mapper = new Mapper();
        $mapper->builder()
            ->element($mapperTypeElement)
            ->setMethods($mappingMethods)
            ->fields($fields)
            ->constructorFragments($constructorFragments)
            ->options($this->options)
            ->versionInformation(null)
            ->decorator($this->getDecorator($mapperTypeElement, $methods, $mapperOptions))
            ->typeFactory($this->typeFactory)
            ->build();
        return $mapper;
    }

    private function initReferencedMappers(\ReflectionClass $element, MapperOptions $mapperAnnotation): array
    {
        $result = $variableNames = [];
        return $result;
    }

    /**
     * @param SourceMethod[] $methods
     * @return BeanMappingMethod[]
     */
    private function getMappingMethods(MapperOptions $mapperAnnotation, array $methods): array
    {
        /** @var MappingMethod[] $mappingMethods */
        $mappingMethods = [];

        foreach ($methods as $method) {
            if (! $method->overridesMethod()) {
                continue;
            }

            $this->mergeInheritedOptions($method, $mapperAnnotation, $methods);
            $mappingOptions = $method->getOptions();
            $hasFactoryMethod = false;

            // TODO: IterableMapping
            // TODO: MapMapping
            // TODO: ValueMapping
            // TODO: RemovedEnumMapping
            // TODO: StreamMapping

            // TODO
            $builder = $method->getOptions()->getBeanMapping()->getBuilder();
            $userDefinedReturnType = $this->getUserDesiredReturnType($method);
            $builderBaseType = $userDefinedReturnType != null ? $userDefinedReturnType : $method->getReturnType();

            // TODO: 没有建造者
//            $this->typeFactory->builderTypeFor($builderBaseType, $builder);

            $beanMappingBuilder = new BeanMappingMethodBuilder();
            $beanMappingMethod = $beanMappingBuilder
                ->mappingContext($this->mappingContext)
                ->sourceMethod($method)
                ->returnTypeBuilder(null)
                ->build();
//            dump($beanMappingMethod);

            $hasFactoryMethod = true;
            if ($beanMappingMethod != null) {
                $mappingMethods[] = $beanMappingMethod;
            }

            if (! $hasFactoryMethod) {
                // TODO: 没有工场类时需要处理额外的逻辑

                // A factory method  is allowed to return an interface type and hence, the generated
                // implementation as well. The check below must only be executed if there's no factory
                // method that could be responsible.
                continue;
            }
        }

        return $mappingMethods;
    }

    /**
     * @param SourceMethod[] $availableMethods
     */
    private function mergeInheritedOptions(SourceMethod $method, MapperOptions $mapperAnnotation, array $availableMethods, array $initializingMethods = [])
    {
        // TODO: 第一期不支持循环
        if (in_array($method, $initializingMethods)) {
            // 循环检查
            // cycle detected
            $initializingMethods[] = $method;

            // TODO: 日志
            echo $method->getName() . '循环了';
            return;
        }

        $initializingMethods[] = $method;

        // TODO
        $mappingOptions = $method->getOptions();

        $mappingOptions->markAsFullyInitialized();
    }

    private function getUserDesiredReturnType(SourceMethod $method): ?Type
    {
        // IterableMapping, BeanMapping, PropertyMapping, MapMapping 才需要用到
        return null;
    }

    private function getDecorator(\ReflectionClass $element, array $methods, MapperOptions $mapperOptions): ?Decorator
    {
        // TODO: 装饰器
        return null;
    }
}
