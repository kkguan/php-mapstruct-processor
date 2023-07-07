<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor;

abstract class AbstractAccessor implements Accessor
{
    protected \ReflectionMethod|\ReflectionProperty|\ReflectionParameter $element;

    public function __construct(\ReflectionMethod|\ReflectionProperty|\ReflectionParameter $element)
    {
        $this->element = $element;
    }

    public function getElement()
    {
        return $this->element;
    }

    public function getSimpleName(): string
    {
        if ($this->element instanceof \ReflectionMethod) {
            return $this->element->getName();
        }

        if ($this->element instanceof \ReflectionProperty) {
            return $this->element->getName();
        }

        if ($this->element instanceof \ReflectionParameter) {
            return $this->element->getName();
        }

        return $this->element->getShortName();
    }

    public function getModifiers()
    {
        return $this->element->getModifiers();
    }
}
