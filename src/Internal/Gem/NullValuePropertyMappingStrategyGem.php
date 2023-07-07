<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Gem;

class NullValuePropertyMappingStrategyGem
{
    public const IGNORE = 'IGNORE';

    public const SET_TO_NULL = 'SET_TO_NULL';

    public const SET_TO_DEFAULT = 'SET_TO_DEFAULT';

    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): NullValuePropertyMappingStrategyGem
    {
        $this->value = $value;
        return $this;
    }
}
