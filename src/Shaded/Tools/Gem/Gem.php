<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Shaded\Tools\Gem;

interface Gem
{
    public function mirror();

    public function isValid(): bool;
}
