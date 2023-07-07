<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Beanmapping;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor\PresenceCheckAccessor;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor\ReadAccessor;

class PropertyEntry
{
    public function __construct(
        private array $fullName,
        private ReadAccessor $readAccessor,
        private ?PresenceCheckAccessor $presenceChecker,
        private ?Type $type,
    ) {
    }

    public static function forSourceReference(array $name, ReadAccessor $readAccessor, ?PresenceCheckAccessor $presenceChecker, ?Type $type): static
    {
        return new static(fullName: $name, readAccessor: $readAccessor, presenceChecker: $presenceChecker, type: $type);
    }

    public function getName()
    {
        return $this->fullName[0];
    }

    public function getReadAccessor(): ReadAccessor
    {
        return $this->readAccessor;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function getPresenceChecker(): ?PresenceCheckAccessor
    {
        return $this->presenceChecker;
    }
}
