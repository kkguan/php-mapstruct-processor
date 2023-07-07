<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Util;

class Fields
{
    private function __construct()
    {
    }

    public static function isFieldAccessor(\ReflectionProperty $method): bool
    {
        return static::isPublic($method) && static::isNotStatic($method);
    }

    private static function isPublic(\ReflectionProperty $method): bool
    {
        if ($method->getModifiers() & \ReflectionProperty::IS_PUBLIC) {
            return true;
        }

        return false;
    }

    private static function isNotStatic(\ReflectionProperty $method): bool
    {
        if ($method->getModifiers() & \ReflectionProperty::IS_STATIC) {
            return false;
        }

        return true;
    }
}
