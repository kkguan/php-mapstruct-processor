<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Beanmapping\PropertyEntry;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Presence\SuffixPresenceCheck;

class SafePropertyEntry
{
    private string $sageName;

    private string $readAccessorName;

    private ?SuffixPresenceCheck $presenceChecker = null;

    private string $previousPropertyName;

    private Type $type;

    public function __construct(PropertyEntry $entry, string $sageName, string $previousPropertyName)
    {
        $this->sageName = $sageName;
        $this->readAccessorName = $entry->getReadAccessor()->getReadValueSource();
        $presenceChecker = $entry->getPresenceChecker();
        if (! empty($presenceChecker)) {
            $this->presenceChecker = new SuffixPresenceCheck($previousPropertyName, $presenceChecker->getPresenceCheckSuffix());
        } else {
            $this->presenceChecker = null;
        }

        $this->previousPropertyName = $previousPropertyName;
        $this->type = $entry->getType();
    }

    public function getName(): string
    {
        return $this->sageName;
    }

    public function getSageName(): string
    {
        return $this->sageName;
    }

    public function setSageName(string $sageName): SafePropertyEntry
    {
        $this->sageName = $sageName;
        return $this;
    }

    public function getReadAccessorName(): string
    {
        return $this->readAccessorName;
    }

    public function setReadAccessorName(string $readAccessorName): SafePropertyEntry
    {
        $this->readAccessorName = $readAccessorName;
        return $this;
    }

    public function getPresenceChecker(): ?SuffixPresenceCheck
    {
        return $this->presenceChecker;
    }

    public function setPresenceChecker(?SuffixPresenceCheck $presenceChecker): SafePropertyEntry
    {
        $this->presenceChecker = $presenceChecker;
        return $this;
    }

    public function getPreviousPropertyName(): string
    {
        return $this->previousPropertyName;
    }

    public function setPreviousPropertyName(string $previousPropertyName): SafePropertyEntry
    {
        $this->previousPropertyName = $previousPropertyName;
        return $this;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function setType(Type $type): SafePropertyEntry
    {
        $this->type = $type;
        return $this;
    }

    public function getAccessorName(): string
    {
        return $this->readAccessorName;
    }
}
