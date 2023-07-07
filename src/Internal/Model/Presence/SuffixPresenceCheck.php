<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Presence;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\ModelElement;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\PresenceCheck;

class SuffixPresenceCheck extends ModelElement implements PresenceCheck
{
    private string $sourceReference;

    private string $suffix;

    public function __construct(string $sourceReference, string $suffix)
    {
        $this->sourceReference = $sourceReference;
        $this->suffix = $suffix;
    }

    public function getImportTypes(): ?array
    {
        return [];
    }

    public function getSourceReference(): string
    {
        return $this->sourceReference;
    }

    public function setSourceReference(string $sourceReference): SuffixPresenceCheck
    {
        $this->sourceReference = $sourceReference;
        return $this;
    }

    public function getSuffix(): string
    {
        return $this->suffix;
    }

    public function setSuffix(string $suffix): SuffixPresenceCheck
    {
        $this->suffix = $suffix;
        return $this;
    }
}
