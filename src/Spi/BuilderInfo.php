<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Spi;

class BuilderInfo
{
    /**
     * @param \ReflectionMethod[] $buildMethods
     */
    public function __construct(
        private \ReflectionMethod $builderCreationMethod,
        private array $buildMethods,
    ) {
    }

    public function getBuilderCreationMethod(): \ReflectionMethod
    {
        return $this->builderCreationMethod;
    }

    public function setBuilderCreationMethod(\ReflectionMethod $builderCreationMethod): BuilderInfo
    {
        $this->builderCreationMethod = $builderCreationMethod;
        return $this;
    }

    public function getBuildMethods(): array
    {
        return $this->buildMethods;
    }

    /**
     * @param \ReflectionMethod[] $buildMethods
     */
    public function setBuildMethods(array $buildMethods): BuilderInfo
    {
        $this->buildMethods = $buildMethods;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @param mixed $buildMethods
     */
    public function addBuildMethods($buildMethods): BuilderInfo
    {
        $this->buildMethods[] = $buildMethods;
        return $this;
    }
}
