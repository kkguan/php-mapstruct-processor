<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor;

interface Accessor
{
    public function getAccessedType();

    public function getElement();

    public function getSimpleName(): string;

    public function getModifiers();

    public function getAccessorType();
}
