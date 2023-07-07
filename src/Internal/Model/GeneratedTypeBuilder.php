<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\TypeFactory;
use Kkguan\PHPMapstruct\Processor\Internal\Option\Options;
use Kkguan\PHPMapstruct\Processor\Internal\Version\VersionInformation;

abstract class GeneratedTypeBuilder
{
    protected TypeFactory $typeFactory;

    // protected ElementUtils elementUtils;

    protected Options $options;

    protected ?VersionInformation $versionInformation;

    protected array $extraImportedTypes;

    /**
     * @var MappingMethod[]
     */
    private array $methods;

    public function typeFactory(TypeFactory $typeFactory): static
    {
        $this->typeFactory = $typeFactory;
        return $this;
    }

    public function options(Options $options): static
    {
        $this->options = $options;
        return $this;
    }

    public function versionInformation(?VersionInformation $versionInformation): static
    {
        $this->versionInformation = $versionInformation;
        return $this;
    }

    public function extraImports(array $extraImportedTypes): static
    {
        $this->extraImportedTypes = $extraImportedTypes;
        return $this;
    }

    /**
     * @var MappingMethod[]
     */
    public function setMethods(array $methods): static
    {
        $this->methods = $methods;
        return $this;
    }

     public function getMethods(): array
     {
         return $this->methods;
     }
}
