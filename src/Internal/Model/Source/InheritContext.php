<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Source;

class InheritContext
{
    public function __construct(private bool $isReversed, private bool $isForwarded, private Method $templateMethod)
    {
    }

    public function isReversed(): bool
    {
        return $this->isReversed;
    }

    public function isForwarded(): bool
    {
        return $this->isForwarded;
    }

    public function getTemplateMethod(): Method
    {
        return $this->templateMethod;
    }
}
