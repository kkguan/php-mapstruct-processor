<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor;

class PresenceCheckAccessor
{
    private function __construct(
        private $suffix,
    ) {
    }

    public function getPresenceCheckSuffix()
    {
        return ($this->suffix)();
    }

    public static function methodInvocation(\ReflectionMethod $element): static
    {
        return new static(function () use ($element) {
            return '->' . $element->getShortName() . '()';
        });
    }
}
