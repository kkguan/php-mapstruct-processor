<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor;

abstract class DelegateAccessor implements Accessor
{
    public function __construct(
        protected Accessor $delegate
    ) {
    }

    public function getAccessedType()
    {
        return $this->delegate->getAccessedType();
    }

    public function getElement()
    {
        return $this->delegate->getElement();
    }

    public function getSimpleName(): string
    {
        return $this->delegate->getSimpleName();
    }

    public function getModifiers()
    {
        return $this->delegate->getModifiers();
    }

    public function getAccessorType()
    {
        return $this->delegate->getAccessorType();
    }
}
