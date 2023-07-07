<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Source;

class Accessibility
{
    public const PUBLIC = 'public';

    public const PROTECTED = 'protected';

    public const PRIVATE = 'private';

    private $accessibility = '';

    public function __construct($accessibility)
    {
        $this->accessibility = $accessibility;
    }

    public static function fromModifiers($modifiers): static
    {
        $accessibility = '';
        if ($modifiers & \ReflectionMethod::IS_PUBLIC) {
            $accessibility = static::PUBLIC;
        } elseif ($modifiers & \ReflectionMethod::IS_PROTECTED) {
            $accessibility = static::PROTECTED;
        } elseif ($modifiers & \ReflectionMethod::IS_PRIVATE) {
            $accessibility = static::PRIVATE;
        }

        return new static($accessibility);
    }

    public function getAccessibility(): string
    {
        return $this->accessibility;
    }

    public function isPublic(): bool
    {
        return $this->accessibility === static::PUBLIC;
    }

    public function isProtected(): bool
    {
        return $this->accessibility === static::PROTECTED;
    }

    public function isPrivate(): bool
    {
        return $this->accessibility === static::PRIVATE;
    }
}
