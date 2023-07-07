<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Processor\Creation;

class BestMatchType
{
    public const IGNORE_QUALIFIERS_BEFORE_Y_CANDIDATES = 1;

    public const IGNORE_QUALIFIERS_AFTER_Y_CANDIDATES = 2;

    private int $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): BestMatchType
    {
        $this->value = $value;
        return $this;
    }
}
