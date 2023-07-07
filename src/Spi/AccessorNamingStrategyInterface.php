<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Spi;

interface AccessorNamingStrategyInterface
{
    public function getMethodType(\ReflectionMethod $executable): string;

    public function getPropertyName(\ReflectionMethod $executable): string;
}
