<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Source;

use Kkguan\PHPMapstruct\Processor\Internal\Gem\BuilderGem;

abstract class DelegatingOptions
{
    private ?DelegatingOptions $next;

    public function __construct(?DelegatingOptions $next)
    {
        $this->next = $next;
    }

    public function getBuilder(): ?BuilderGem
    {
        return $this->next?->getBuilder();
    }
}
