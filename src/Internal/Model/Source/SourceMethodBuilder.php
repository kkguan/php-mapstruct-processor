<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Source;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Parameter;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\TypeFactory;

class SourceMethodBuilder
{
    private \ReflectionMethod $executable;

    /** @var Parameter[] */
    private array $parameters;

    private ?Type $returnType;

    private ?Type $declaringMapper = null;

    private ?Type $definingType = null;

    private array $exceptionTypes = [];

    /** @var MappingOptions[] */
    private array $mappingOptions = [];

    private ?IterableMappingOptions $iterableMappingOptionss = null;

    private ?MapMappingOptions $mapMappingOptions = null;

    private ?BeanMappingOptions $beanMapping = null;

    private TypeFactory $typeFactory;

    private ?MapperOptions $mapper = null;

    /** @var SourceMethod[] */
    private array $prototypeMethods = [];

    /** @var ValueMappingOptions[] */
    private array $valueMappingOptionss = [];

    private ?EnumMappingOptions $enumMappingOptions = null;

    private ?ParameterProvidedMethods $contextProvidedMethods = null;

    /** @var Type[] */
    private array $typeParameters;

    /** @var SubclassMappingOptions[] */
    private array $subclassMappings = [];

    private bool $verboseLogging = false;

    private ?SubclassValidator $subclassValidator = null;

    public function getExecutable(): \ReflectionMethod
    {
        return $this->executable;
    }

    public function setExecutable(\ReflectionMethod $executable): SourceMethodBuilder
    {
        $this->executable = $executable;
        return $this;
    }

    /**
     * @return Parameter[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param Parameter[] $parameters
     */
    public function setParameters(array $parameters): SourceMethodBuilder
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function getReturnType(): ?Type
    {
        return $this->returnType;
    }

    public function setReturnType(?Type $returnType): SourceMethodBuilder
    {
        $this->returnType = $returnType;
        return $this;
    }

    public function getDeclaringMapper(): ?Type
    {
        return $this->declaringMapper;
    }

    public function setDeclaringMapper(?Type $declaringMapper): SourceMethodBuilder
    {
        $this->declaringMapper = $declaringMapper;
        return $this;
    }

    public function getDefiningType(): ?Type
    {
        return $this->definingType;
    }

    public function setDefiningType(?Type $definingType): SourceMethodBuilder
    {
        $this->definingType = $definingType;
        return $this;
    }

    public function getExceptionTypes(): array
    {
        return $this->exceptionTypes;
    }

    public function setExceptionTypes(array $exceptionTypes): SourceMethodBuilder
    {
        $this->exceptionTypes = $exceptionTypes;
        return $this;
    }

    /**
     * @return MappingOptions[]
     */
    public function getMappingOptions(): array
    {
        return $this->mappingOptions;
    }

    /**
     * @param MappingOptions[] $mappingOptions
     */
    public function setMappingOptions(array $mappingOptions): SourceMethodBuilder
    {
        $this->mappingOptions = $mappingOptions;
        return $this;
    }

    public function getIterableMappingOptionss(): ?IterableMappingOptions
    {
        return $this->iterableMappingOptionss;
    }

    public function setIterableMappingOptionss(?IterableMappingOptions $iterableMappingOptionss): SourceMethodBuilder
    {
        $this->iterableMappingOptionss = $iterableMappingOptionss;
        return $this;
    }

    public function getMapMappingOptions(): ?MapMappingOptions
    {
        return $this->mapMappingOptions;
    }

    public function setMapMappingOptions(?MapMappingOptions $mapMappingOptions): SourceMethodBuilder
    {
        $this->mapMappingOptions = $mapMappingOptions;
        return $this;
    }

    public function getBeanMapping(): ?BeanMappingOptions
    {
        return $this->beanMapping;
    }

    public function setBeanMapping(?BeanMappingOptions $beanMapping): SourceMethodBuilder
    {
        $this->beanMapping = $beanMapping;
        return $this;
    }

    public function getTypeFactory(): TypeFactory
    {
        return $this->typeFactory;
    }

    public function setTypeFactory(TypeFactory $typeFactory): SourceMethodBuilder
    {
        $this->typeFactory = $typeFactory;
        return $this;
    }

    public function getMapper(): ?MapperOptions
    {
        return $this->mapper;
    }

    public function setMapper(MapperOptions $mapper): SourceMethodBuilder
    {
        $this->mapper = $mapper;
        return $this;
    }

    /**
     * @return SourceMethod[]
     */
    public function getPrototypeMethods(): array
    {
        return $this->prototypeMethods;
    }

    /**
     * @param SourceMethod[] $prototypeMethods
     */
    public function setPrototypeMethods(array $prototypeMethods): SourceMethodBuilder
    {
        $this->prototypeMethods = $prototypeMethods;
        return $this;
    }

    /**
     * @return ValueMappingOptions[]
     */
    public function getValueMappingOptionss(): array
    {
        return $this->valueMappingOptionss;
    }

    /**
     * @param ValueMappingOptions[] $valueMappingOptionss
     */
    public function setValueMappingOptionss(array $valueMappingOptionss): SourceMethodBuilder
    {
        $this->valueMappingOptionss = $valueMappingOptionss;
        return $this;
    }

    public function getEnumMappingOptions(): ?EnumMappingOptions
    {
        return $this->enumMappingOptions;
    }

    public function setEnumMappingOptions(?EnumMappingOptions $enumMappingOptions): SourceMethodBuilder
    {
        $this->enumMappingOptions = $enumMappingOptions;
        return $this;
    }

    public function getContextProvidedMethods(): ?ParameterProvidedMethods
    {
        return $this->contextProvidedMethods;
    }

    public function setContextProvidedMethods(?ParameterProvidedMethods $contextProvidedMethods): SourceMethodBuilder
    {
        $this->contextProvidedMethods = $contextProvidedMethods;
        return $this;
    }

    /**
     * @return Type[]
     */
    public function getTypeParameters(): array
    {
        return $this->typeParameters;
    }

    /**
     * @param Type[] $typeParameters
     */
    public function setTypeParameters(array $typeParameters): SourceMethodBuilder
    {
        $this->typeParameters = $typeParameters;
        return $this;
    }

    /**
     * @return SubclassMappingOptions[]
     */
    public function getSubclassMappings(): array
    {
        return $this->subclassMappings;
    }

    /**
     * @param SubclassMappingOptions[] $subclassMappings
     */
    public function setSubclassMappings(array $subclassMappings): SourceMethodBuilder
    {
        $this->subclassMappings = $subclassMappings;
        return $this;
    }

    public function isVerboseLogging(): bool
    {
        return $this->verboseLogging;
    }

    public function setVerboseLogging(bool $verboseLogging): SourceMethodBuilder
    {
        $this->verboseLogging = $verboseLogging;
        return $this;
    }

    public function getSubclassValidator(): ?SubclassValidator
    {
        return $this->subclassValidator;
    }

    public function setSubclassValidator(?SubclassValidator $subclassValidator): SourceMethodBuilder
    {
        $this->subclassValidator = $subclassValidator;
        return $this;
    }

    public function build(): SourceMethod
    {
        $mappingMethodOptions = new MappingMethodOptions(
            mapper: $this->mapper,
            mappings: $this->mappingOptions,
            iterableMapping: $this->iterableMappingOptionss,
            mapMapping: $this->mapMappingOptions,
            beanMapping: $this->beanMapping,
            enumMappingOptions: $this->enumMappingOptions,
            valueMappings: $this->valueMappingOptionss,
            subclassMappings: $this->subclassMappings,
            subclassValidator: $this->subclassValidator,
        );

        foreach ($this->executable->getParameters() as $parameter) {
            $this->typeParameters[] = $this->typeFactory->getType($parameter->getType());
        }

        return new SourceMethod($this, $mappingMethodOptions);
    }
}
