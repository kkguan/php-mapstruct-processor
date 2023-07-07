<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Beanmapping;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\MappingOptions;

class MappingReference
{
    public function __construct(
        private MappingOptions $mapping,
        private ?TargetReference $targetReference,
        private ?SourceReference $sourceReference
    ) {
    }

    public function getTargetReference(): ?TargetReference
    {
        return $this->targetReference;
    }

    public function isValid(): bool
    {
        return $this->sourceReference === null || $this->sourceReference->isValid();
    }

    public function getMapping(): MappingOptions
    {
        return $this->mapping;
    }

    public function getSourceReference(): ?SourceReference
    {
        return $this->sourceReference;
    }
}
