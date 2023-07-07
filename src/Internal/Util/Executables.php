<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Util;

class Executables
{
    public static function isPublicNotStatic(\ReflectionMethod $method): bool
    {
        return static::isPublic($method) && static::isNotStatic($method);
    }

    public static function isLifecycleCallbackMethod(\ReflectionMethod $executableElement): bool
    {
        return static::isBeforeMappingMethod($executableElement) || static::isAfterMappingMethod($executableElement);
    }

    private static function isPublic(\ReflectionMethod $method): bool
    {
        if ($method->getModifiers() & \ReflectionMethod::IS_PUBLIC) {
            return true;
        }

        return false;
    }

    private static function isNotStatic(\ReflectionMethod $method): bool
    {
        if ($method->getModifiers() & \ReflectionMethod::IS_STATIC) {
            return false;
        }

        return true;
    }

    private static function isAfterMappingMethod(\ReflectionMethod $executableElement): bool
    {
        // TODO:未实现,暂写死
        return false;
    }

    private static function isBeforeMappingMethod(\ReflectionMethod $executableElement): bool
    {
        // TODO:未实现,暂写死
        return false;
    }
}
