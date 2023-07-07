<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Assignment;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Assignment;

class SetterWrapper extends AssignmentWrapper
{
    public function __construct(
        Assignment $rhs,
        private array $thrownTypesToExclude,
        bool $fieldAssignment,
        private bool $includeSourceNullCheck = false,
        private bool $setExplicitlyToNull = false,
        private bool $setExplicitlyToDefault = false,
    ) {
        parent::__construct($rhs, $fieldAssignment);
    }

    public function getThrownTypesToExclude(): array
    {
        return $this->thrownTypesToExclude;
    }

    public function isIncludeSourceNullCheck(): bool
    {
        return $this->includeSourceNullCheck;
    }

    public function isSetExplicitlyToNull(): bool
    {
        return $this->setExplicitlyToNull;
    }

    public function isSetExplicitlyToDefault(): bool
    {
        return $this->setExplicitlyToDefault;
    }

    public function getTemplate(): string
    {
        return './Assignment/setterWrapper.twig';
    }
}
