<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Source;

class MappingMethodOptions
{
    private ?MapperOptions $mapper;

    /** @var MappingOptions[] */
    private array $mappings;

    private ?IterableMappingOptions $iterableMapping;

    private ?MapMappingOptions $mapMapping;

    private ?BeanMappingOptions $beanMapping;

    private ?EnumMappingOptions $enumMappingOptions;

    /** @var ValueMappingOptions[] */
    private array $valueMappings;

    private bool $fullyInitialized = false;

    /** @var SubclassMappingOptions[] */
    private array $subclassMappings;

    private ?SubclassValidator $subclassValidator;

    /**
     * @param MappingOptions[] $mappings
     * @param ValueMappingOptions[] $valueMappings
     * @param SubclassMappingOptions[] $subclassMappings
     */
    public function __construct(
        ?MapperOptions $mapper,
        array $mappings,
        ?IterableMappingOptions $iterableMapping,
        ?MapMappingOptions $mapMapping,
        ?BeanMappingOptions $beanMapping,
        ?EnumMappingOptions $enumMappingOptions,
        array $valueMappings,
        array $subclassMappings,
        ?SubclassValidator $subclassValidator
    ) {
        $this->mapper = $mapper;
        $this->mappings = $mappings;
        $this->iterableMapping = $iterableMapping;
        $this->mapMapping = $mapMapping;
        $this->beanMapping = $beanMapping;
        $this->enumMappingOptions = $enumMappingOptions;
        $this->valueMappings = $valueMappings;
        $this->subclassMappings = $subclassMappings;
        $this->subclassValidator = $subclassValidator;
    }

    public function markAsFullyInitialized(): void
    {
        $this->fullyInitialized = true;
    }

    public function getBeanMapping(): BeanMappingOptions
    {
        return $this->beanMapping;
    }

    /**
     * @return MappingOptions[]
     */
    public function getMappings(): array
    {
        return $this->mappings;
    }

    public function getMapper(): ?MapperOptions
    {
        return $this->mapper;
    }

    public function getIterableMapping(): ?IterableMappingOptions
    {
        return $this->iterableMapping;
    }

    public function getMapMapping(): ?MapMappingOptions
    {
        return $this->mapMapping;
    }

    public function getEnumMappingOptions(): ?EnumMappingOptions
    {
        return $this->enumMappingOptions;
    }

    public function getValueMappings(): array
    {
        return $this->valueMappings;
    }

    public function isFullyInitialized(): bool
    {
        return $this->fullyInitialized;
    }

    public function getSubclassMappings(): array
    {
        return $this->subclassMappings;
    }

    public function getSubclassValidator(): ?SubclassValidator
    {
        return $this->subclassValidator;
    }

    public static function getForgedMethodInheritedOptions(MappingMethodOptions $options): MappingMethodOptions
    {
        return new MappingMethodOptions(
            $options->getMapper(),
            $options->getMappings(),
            $options->getIterableMapping(),
            $options->getMapMapping(),
            $options->getBeanMapping(),
            $options->getEnumMappingOptions(),
            $options->getValueMappings(),
            [],
            null
        );
    }
}
