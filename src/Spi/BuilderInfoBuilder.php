<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Spi;

use Kkguan\PHPMapstruct\Processor\Exception\IllegalArgumentException;

class BuilderInfoBuilder
{
    private ?\ReflectionMethod $builderCreationMethod = null;

    /** @var \ReflectionMethod[] */
    private ?array $buildMethods = null;

    public function builderCreationMethod(\ReflectionMethod $builderCreationMethod): static
    {
        $this->builderCreationMethod = $builderCreationMethod;
        return $this;
    }

    /** @var \ReflectionMethod[] */
    public function buildMethod(array $buildMethods)
    {
        $this->buildMethods = $buildMethods;
        return $this;
    }

    public function build(): BuilderInfo
    {
        if (empty($this->builderCreationMethod)) {
            throw new IllegalArgumentException('Builder creation method is mandatory');
        }

        if (empty($this->buildMethods)) {
            throw new IllegalArgumentException('Build methods must not be empty');
        }

        return new BuilderInfo($this->builderCreationMethod, $this->buildMethods);
    }
}
