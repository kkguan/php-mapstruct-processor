<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Beanmapping;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Parameter;

class TargetReference
{
    public function __construct(
        private ?Parameter $parameter,
        private array $propertyEntries,
        private array $pathProperties = []
    ) {
    }

    public function isNested(): bool
    {
        return count($this->propertyEntries) > 1;
    }

    public function getShallowestPropertyName()
    {
        if (empty($this->propertyEntries)) {
            return null;
        }

        return $this->propertyEntries[0];
    }

    public function getPropertyEntries(): array
    {
        return $this->propertyEntries;
    }
}
