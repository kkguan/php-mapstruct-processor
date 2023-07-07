<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model;

class MapperBuilder extends GeneratedTypeBuilder
{
    private \ReflectionClass $typeElement;

    private array $fields;

    private array $fragments;

    // private Decorator decorator;

    private string $implName;

    private bool $customName;

    private string $implPackage;

    private bool $customPackage;

    private bool $suppressGeneratorTimestamp;

    private ?Decorator $decorator;

    public function build()
    {
        // TODO: build
        // 由于第一期我们不实现mapper里面的配置参数，所以这块代码意义并不大

//        $definitonType = $this->typeFactory->getType($this->typeElement);

        return (new Mapper())->setElement($this->typeElement)->setNamespace($this->typeElement->getNamespaceName());
    }

    public function getTypeElement(): \ReflectionClass
    {
        return $this->typeElement;
    }

    public function element(\ReflectionClass $typeElement): MapperBuilder
    {
        $this->typeElement = $typeElement;
        return $this;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function fields(array $fields): MapperBuilder
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * @param mixed $fields
     */
    public function addFields($fields): MapperBuilder
    {
        $this->fields[] = $fields;
        return $this;
    }

    public function getFragments(): array
    {
        return $this->fragments;
    }

    public function constructorFragments(array $fragments): MapperBuilder
    {
        $this->fragments = $fragments;
        return $this;
    }

    /**
     * @param mixed $fragments
     */
    public function addFragments($fragments): MapperBuilder
    {
        $this->fragments[] = $fragments;
        return $this;
    }

    public function getImplName(): string
    {
        return $this->implName;
    }

    public function setImplName(string $implName): MapperBuilder
    {
        $this->implName = $implName;
        return $this;
    }

    public function isCustomName(): bool
    {
        return $this->customName;
    }

    public function setCustomName(bool $customName): MapperBuilder
    {
        $this->customName = $customName;
        return $this;
    }

    public function getImplPackage(): string
    {
        return $this->implPackage;
    }

    public function setImplPackage(string $implPackage): MapperBuilder
    {
        $this->implPackage = $implPackage;
        return $this;
    }

    public function isCustomPackage(): bool
    {
        return $this->customPackage;
    }

    public function setCustomPackage(bool $customPackage): MapperBuilder
    {
        $this->customPackage = $customPackage;
        return $this;
    }

    public function isSuppressGeneratorTimestamp(): bool
    {
        return $this->suppressGeneratorTimestamp;
    }

    public function setSuppressGeneratorTimestamp(bool $suppressGeneratorTimestamp): MapperBuilder
    {
        $this->suppressGeneratorTimestamp = $suppressGeneratorTimestamp;
        return $this;
    }

    public function decorator(?Decorator $decorator): MapperBuilder
    {
        $this->decorator = $decorator;
        return $this;
    }
}
