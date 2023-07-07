<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\TypeFactory;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\SourceMethod;
use Kkguan\PHPMapstruct\Processor\Internal\Option\Options;
use Kkguan\PHPMapstruct\Processor\Internal\Processor\Creation\MappingResolver;
use Kkguan\PHPMapstruct\Processor\Internal\Util\AccessorNamingUtils;
use Psr\Log\LoggerInterface;

class MappingBuilderContext
{
    private $enumMappingStrategy;

    private $enumTransformationStrategies;

    /**
     * @var MappingMethod[]
     */
    private array $mappingsToGenerate = [];

    /**
     * @param SourceMethod[] $sourceModel
     */
    public function __construct(
        private typeFactory $typeFactory,
        private AccessorNamingUtils $accessorNaming,
        private Options $options,
        private MappingResolver $mappingResolver,
        private \ReflectionClass $mapperTypeElement,
        private array $sourceModel,
        private array $mapperReferences,
    ) {
    }

    public function getTypeFactory(): TypeFactory
    {
        return $this->typeFactory;
    }

    public function getMappingResolver(): MappingResolver
    {
        return $this->mappingResolver;
    }

    /**
     * @return MappingMethod[]
     */
    public function getMappingsToGenerate(): array
    {
        return $this->mappingsToGenerate;
    }

    public function addMappingsToGenerate(MappingMethod $mappingMethod): static
    {
        $this->mappingsToGenerate[] = $mappingMethod;
        return $this;
    }

    public function getUsedSupportedMappings()
    {
        return $this->mappingResolver->getUsedSupportedMappings();
    }

    public function getMapperReferences()
    {
        return $this->mapperReferences;
    }

    public function getUsedSupportedFields()
    {
        return $this->mappingResolver->getUsedSupportedFields();
    }

    public function getMessager(): LoggerInterface
    {
        return $this->getOptions()->getLogger();
        // TODO: 这里最好是从容器内获取一个 LoggerFactory 出来
//        return new \Kkguan\PHPMapstruct\Processor\Internal\Util\LoggerFactory();
    }

    public function getEnumMappingStrategy()
    {
        return $this->enumMappingStrategy;
    }

    public function getEnumTransformationStrategies()
    {
        return $this->enumTransformationStrategies;
    }

    public function getAccessorNaming(): AccessorNamingUtils
    {
        return $this->accessorNaming;
    }

    public function getOptions(): Options
    {
        return $this->options;
    }

    public function getMapperTypeElement(): \ReflectionClass
    {
        return $this->mapperTypeElement;
    }

    public function getSourceModel(): array
    {
        return $this->sourceModel;
    }

    public function getExistingMappingMethod(MappingMethod $newMappingMethod): ?MappingMethod
    {
        $existingMappingMethod = null;
        foreach ($this->mappingsToGenerate as $mappingMethod) {
            if ($mappingMethod->getName() == $newMappingMethod->getName()) {
                $existingMappingMethod = $mappingMethod;
                break;
            }
        }

        return $existingMappingMethod;
    }

    public function getReservedNames(): array
    {
        $nameSet = [];

        foreach ($this->mappingsToGenerate as $method) {
            $nameSet[] = $method->getName();
        }
        // add existing names
        foreach ($this->sourceModel as $method) {
            if ($method->isAbstract()) {
                $nameSet[] = $method->getName();
            }
        }

        return $nameSet;
    }
}
