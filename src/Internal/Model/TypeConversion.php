<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Assignment;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\AssignmentType;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\ModelElement;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\PresenceCheck;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;

class TypeConversion extends ModelElement implements Assignment
{
    public const SOURCE_REFERENCE_PATTERN = '<SOURCE>';

    private array $importTypes = [];

    private array $thrownTypes = [];

    private ?Assignment $assignment = null;

    private string $openExpression;

    private string $closeExpression;

    public function __construct(array $importTypes, array $exceptionTypes, string $expression)
    {
        $this->importTypes = array_merge($importTypes, $exceptionTypes);
        $this->thrownTypes = $exceptionTypes;
        $patternIndex = strpos($expression, self::SOURCE_REFERENCE_PATTERN);
        $this->openExpression = substr($expression, 0, $patternIndex);
        $this->closeExpression = substr($expression, $patternIndex + strlen(self::SOURCE_REFERENCE_PATTERN));
    }

    public function __toString(): string
    {
        $argument = $this->assignment != null ? (string) $this->assignment : $this->getSourceReference();
        return $this->openExpression . $argument . $this->closeExpression;
    }

    public function getThrownTypes(): array
    {
        return $this->thrownTypes;
    }

    public function setAssignment(Assignment $assignment)
    {
        $this->assignment = $assignment;
    }

    public function getAssignment(): Assignment
    {
        return $this->assignment;
    }

    public function getOpenExpression(): string
    {
        return $this->openExpression;
    }

    public function getCloseExpression(): string
    {
        return $this->closeExpression;
    }

    public function getSourceReference(): string
    {
        return $this->assignment->getSourceReference();
    }

    public function isSourceReferenceParameter(): bool
    {
        return $this->assignment->isSourceReferenceParameter();
    }

    public function getSourcePresenceCheckerReference(): ?PresenceCheck
    {
        return $this->assignment->getSourcePresenceCheckerReference();
    }

    public function getSourceType(): Type
    {
        return $this->assignment->getSourceType();
    }

    public function createUniqueVarName(string $desiredName): string
    {
        return $this->assignment->createUniqueVarName($desiredName);
    }

    public function getSourceLocalVarName(): ?string
    {
        return $this->assignment->getSourceLocalVarName();
    }

    public function getSourceParameterName(): string
    {
        return $this->assignment->getSourceParameterName();
    }

    public function setSourceLocalVarName(?string $sourceLocalVarName)
    {
        return $this->assignment->setSourceLocalVarName($sourceLocalVarName);
    }

    public function getSourceLoopVarName(): ?string
    {
        return $this->assignment->getSourceLoopVarName();
    }

    public function setSourceLoopVarName(string $sourceLoopVarName): void
    {
        $this->assignment->setSourceLoopVarName($sourceLoopVarName);
    }

    public function getType(): ?AssignmentType
    {
        switch ($this->assignment->getType()->getType()) {
            case AssignmentType::DIRECT:
                return new AssignmentType(AssignmentType::TYPE_CONVERTED);
            case AssignmentType::MAPPED:
                return new AssignmentType(AssignmentType::MAPPED_TYPE_CONVERTED);
            default:
                return null;
        }
    }

    public function isCallingUpdateMethod(): bool
    {
        return false;
    }

    public function getImportTypes(): ?array
    {
        return $this->importTypes;
    }
}
