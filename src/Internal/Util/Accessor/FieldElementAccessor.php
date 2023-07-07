<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor;

class FieldElementAccessor extends AbstractAccessor
{
    public function __construct(\ReflectionProperty $element)
    {
        parent::__construct($element);
    }

    public function getAccessedType()
    {
        return $this->element->getType();
    }

    public function getAccessorType()
    {
        return AccessorType::FIELD;
    }
}
