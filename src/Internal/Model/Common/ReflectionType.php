<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Common;

class ReflectionType extends \ReflectionNamedType
{
    private const BUILT_IN_TYPES = ['int' => null, 'float' => null, 'string' => null, 'bool' => null, 'callable' => null, 'self' => null, 'parent' => null, 'array' => null, 'iterable' => null, 'object' => null, 'void' => null, 'mixed' => null, 'static' => null];

    private string $self_name;

    private bool $self_allows_null;

    public function __construct(string $name, bool $allowsNull = false)
    {
        $this->self_name = $name;
        $this->self_allows_null = $allowsNull;
    }

    public function __toString(): string
    {
        $name = '';
        if ($this->allowsNull()) {
            $name .= '?';
        }
        return $name . $this->getName();
    }

    public function getName(): string
    {
        return $this->self_name;
    }

    public function allowsNull(): bool
    {
        return $this->self_allows_null;
    }

    public function isBuiltin(): bool
    {
        if (array_key_exists($this->self_name, self::BUILT_IN_TYPES)) {
            return true;
        }
        return false;
    }
}
