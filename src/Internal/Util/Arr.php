<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Util;

class Arr
{
    public static function mixedToArray(mixed $mixed): array
    {
        if (is_array($mixed)) {
            return $mixed;
        }

        return [$mixed];
    }
}
