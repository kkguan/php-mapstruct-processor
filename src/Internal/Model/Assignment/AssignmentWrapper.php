<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Assignment;

use Kkguan\PHPMapstruct\Processor\Exception\UnsupportedOperationException;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Assignment;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\AssignmentType;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\ModelElement;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\PresenceCheck;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;

abstract class AssignmentWrapper extends ModelElement implements Assignment
{
    private Assignment $decoratedAssignment;

    private bool $fieldAssignment;

    public function __construct(Assignment $decoratedAssignment, bool $fieldAssignment)
    {
        $this->decoratedAssignment = $decoratedAssignment;
        $this->fieldAssignment = $fieldAssignment;
    }

    public function getDecoratedAssignment(): Assignment
    {
        return $this->decoratedAssignment;
    }

    public function setDecoratedAssignment(Assignment $decoratedAssignment): AssignmentWrapper
    {
        $this->decoratedAssignment = $decoratedAssignment;
        return $this;
    }

    public function isFieldAssignment(): bool
    {
        return $this->fieldAssignment;
    }

    public function setFieldAssignment(bool $fieldAssignment): AssignmentWrapper
    {
        $this->fieldAssignment = $fieldAssignment;
        return $this;
    }

    public function getImportTypes(): ?array
    {
        return $this->decoratedAssignment->getImportTypes();
    }

    public function getThrownTypes(): array
    {
        return $this->decoratedAssignment->getThrownTypes();
    }

    public function setAssignment(Assignment $assignment)
    {
        throw new UnsupportedOperationException('deliberately not implemented');
    }

    public function getAssignment(): Assignment
    {
        return $this->decoratedAssignment;
    }

    public function getSourceReference(): string
    {
        return $this->decoratedAssignment->getSourceReference();
    }

    public function isSourceReferenceParameter(): bool
    {
        return $this->decoratedAssignment->isSourceReferenceParameter();
    }

    public function getSourcePresenceCheckerReference(): ?PresenceCheck
    {
        return $this->decoratedAssignment->getSourcePresenceCheckerReference();
    }

    public function getSourceType(): Type
    {
        return $this->decoratedAssignment->getSourceType();
    }

    public function getSourceLocalVarName(): ?string
    {
        return $this->decoratedAssignment->getSourceLocalVarName();
    }

    public function setSourceLocalVarName(?string $sourceLocalVarName): AssignmentWrapper
    {
        $this->decoratedAssignment->setSourceLocalVarName($sourceLocalVarName);
        return $this;
    }

    public function getSourceLoopVarName(): string
    {
        return $this->decoratedAssignment->getSourceLoopVarName();
    }

    public function setSourceLoopVarName(?string $sourceLoopVarName): void
    {
        $this->decoratedAssignment->setSourceLoopVarName($sourceLoopVarName);
    }

    public function getSourceParameterName(): string
    {
        return $this->decoratedAssignment->getSourceParameterName();
    }

    public function getType(): ?AssignmentType
    {
        return $this->decoratedAssignment->getType();
    }

    public function isCallingUpdateMethod(): bool
    {
        return $this->decoratedAssignment->isCallingUpdateMethod();
    }

    public function createUniqueVarName(string $desiredName): string
    {
        return $this->decoratedAssignment->createUniqueVarName($desiredName);
    }

    public function getTemplate(): string
    {
        return 'assignmentWrapper.twig';
    }
}
