<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Processor\Creation;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Assignment;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;

class ConversionAssignment
{
    public function __construct(
        private Type $sourceType,
        private Type $target,
        private Assignment $assignment,
    ) {
    }

    public function getSourceType(): Type
    {
        return $this->sourceType;
    }

    public function getTarget(): Type
    {
        return $this->target;
    }

    public function getAssignment(): Assignment
    {
        return $this->assignment;
    }

    public function getShortName()
    {
        return $this->sourceType->getName() . '-->' . $this->target->getName();
    }

    public function reportMessageWhenNarrowing()
    {
    }
}
