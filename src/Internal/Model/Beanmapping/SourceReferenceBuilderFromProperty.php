<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Beanmapping;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Parameter;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor\PresenceCheckAccessor;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor\ReadAccessor;

class SourceReferenceBuilderFromProperty
{
    private Parameter $sourceParameter;

    private ?Type $type;

    private ReadAccessor $readAccessor;

    private ?PresenceCheckAccessor $presenceChecker;

    private string $name;

    public function sourceParameter(Parameter $sourceParameter)
    {
        $this->sourceParameter = $sourceParameter;
        return $this;
    }

    public function type(?Type $type)
    {
        $this->type = $type;
        return $this;
    }

    public function readAccessor(ReadAccessor $readAccessor)
    {
        $this->readAccessor = $readAccessor;
        return $this;
    }

    public function presenceChecker(?PresenceCheckAccessor $presenceChecker)
    {
        $this->presenceChecker = $presenceChecker;
        return $this;
    }

    public function name(string $name)
    {
        $this->name = $name;
        return $this;
    }

    public function build(): SourceReference
    {
        $sourcePropertyEntries = [];
        if ($this->readAccessor !== null) {
            $sourcePropertyEntries[] = PropertyEntry::forSourceReference(
                name: [$this->name],
                readAccessor: $this->readAccessor,
                presenceChecker: $this->presenceChecker,
                type: $this->type
            );
        }
        return new SourceReference(sourceParameter: $this->sourceParameter, sourcePropertyEntries: $sourcePropertyEntries, isValid: true);
    }
}
