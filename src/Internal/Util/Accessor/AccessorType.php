<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor;

class AccessorType
{
    public const PARAMETER = 'parameter';

    public const FIELD = 'field';

    public const GETTER = 'getter';

    public const SETTER = 'setter';

    public const ADDER = 'adder';

    public static function isFieldAssignment($type): bool
    {
        return in_array($type, [static::FIELD, static::PARAMETER]);
    }
}
