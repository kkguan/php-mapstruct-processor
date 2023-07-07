<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Util;

use Kkguan\PHPMapstruct\Processor\Spi\AccessorNamingStrategyInterface;
use Kkguan\PHPMapstruct\Processor\Spi\BuilderProvider;
use Kkguan\PHPMapstruct\Processor\Spi\DefaultAccessorNamingStrategy;

class AnnotationProcessorContext
{
    private bool $initialized = false;

    private AccessorNamingStrategyInterface $accessorNamingStrategy;

    private AccessorNamingUtils $accessorNaming;

    private BuilderProvider $builderProvider;

    public function __construct()
    {
    }

    public function getAccessorNaming(): AccessorNamingUtils
    {
        $this->initialize();
        return $this->accessorNaming;
    }

    public function getBuilderProvider(): BuilderProvider
    {
        $this->initialize();
        return $this->builderProvider;
    }

    private function initialize(): void
    {
        if ($this->initialized) {
            return;
        }

        // TODO: 待实现spi机制
        $defaultAccessorNamingStrategy = new DefaultAccessorNamingStrategy();
        $this->accessorNamingStrategy = $defaultAccessorNamingStrategy;

        $this->accessorNaming = new AccessorNamingUtils($this->accessorNamingStrategy);

        $this->initialized = true;
    }
}
