<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor;

class ReadAccessorFactory
{
    public static function fromGetter(\ReflectionMethod $element, \ReflectionType $returnType): ReadAccessor
    {
        return new class(new ExecutableElementAccessor(element: $element, accessedType: $returnType, accessorType: AccessorType::GETTER)) extends ReadDelegateAccessor {
            public function getReadValueSource(): string
            {
                return $this->getSimpleName() . '()';
            }
        };
    }

    public static function fromField(\ReflectionProperty $variableElement): ReadAccessor
    {
        return new class(new FieldElementAccessor($variableElement)) extends ReadDelegateAccessor {
            public function getReadValueSource(): string
            {
                return $this->getSimpleName();
            }
        };
    }
}
