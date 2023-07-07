<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor;

class ParameterElementAccessor extends AbstractAccessor
{
    private string $name;

    public function __construct(\ReflectionParameter $element, string $name)
    {
        parent::__construct($element);
        $this->name = $name;
    }

    public function getSimpleName(): string
    {
        return $this->name;
    }

    public function getAccessedType()
    {
        return $this->element->getType();
    }

    public function getAccessorType()
    {
        return AccessorType::PARAMETER;
    }
}
