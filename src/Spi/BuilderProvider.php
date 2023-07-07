<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Spi;

interface BuilderProvider
{
    public function init();

    /**
     * Find the builder information, if any, for the type.
     */
    public function findBuilderInfo(\ReflectionType $type): BuilderInfo;
}
