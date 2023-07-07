<?php

declare(strict_types=1);
if (! function_exists('enum_exists')) {
    function enum_exists(string $enum, bool $autoload = true): bool
    {
        return false;
    }
}

if (! function_exists('have_enum')) {
    function have_enum(): bool
    {
        return PHP_VERSION_ID >= 80100;
    }
}
