<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor;

class ExecutableElementAccessor extends AbstractAccessor
{
    private \ReflectionParameter|\ReflectionType $accessedType;

    private string $accessorType;

    public function __construct(\ReflectionMethod $element, \ReflectionParameter|\ReflectionType $accessedType, string $accessorType)
    {
        parent::__construct($element);
        $this->element = $element;
        $this->accessedType = $accessedType;
        $this->accessorType = $accessorType;
    }

    public function getAccessedType()
    {
        return $this->accessedType;
    }

    public function getAccessorType()
    {
        return $this->accessorType;
    }
}
