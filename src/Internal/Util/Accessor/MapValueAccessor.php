<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor;

class MapValueAccessor implements ReadAccessor
{
    public function __construct(
        private \ReflectionMethod|\ReflectionProperty|\ReflectionParameter $element,
        private \ReflectionType $valueTypeMirror,
        private string $simpleName
    ) {
    }

    public function getAccessedType()
    {
        return $this->valueTypeMirror;
    }

    public function getElement()
    {
        return $this->element;
    }

    public function getSimpleName(): string
    {
        return $this->simpleName;
    }

    public function getModifiers()
    {
        return [];
    }

    public function getAccessorType()
    {
        return AccessorType::GETTER;
    }

    public function getReadValueSource(): string
    {
        return sprintf('get(%s)', $this->getSimpleName());
    }
}
