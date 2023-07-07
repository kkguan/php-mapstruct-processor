<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Common;

use Kkguan\PHPMapstruct\Processor\Internal\Util\Str;

class SourceRHS implements Assignment
{
    private bool $useElementAsSourceTypeForMatching;

    private ?string $sourceLocalVarName;

    public function __construct(
        private string $sourceParameterName,
        private string $sourceReference,
        private $sourcePresenceCheckerReference,
        private Type $sourceType,
        private array $existingVariableNames,
        private string $sourceErrorMessagePart
    ) {
    }

    public function __toString(): string
    {
        return $this->sourceReference;
    }

    public function setSourcePresenceCheckerReference($sourcePresenceCheckerReference)
    {
        $this->sourcePresenceCheckerReference = $sourcePresenceCheckerReference;
    }

    public function setUseElementAsSourceTypeForMatching(bool $useElementAsSourceTypeForMatching)
    {
        $this->useElementAsSourceTypeForMatching = $useElementAsSourceTypeForMatching;
    }

    public function getSourceType(): Type
    {
        return $this->sourceType;
    }

    public function getSourceTypeForMatching(): Type
    {
        if ($this->useElementAsSourceTypeForMatching) {
            // TODO
        }

        return $this->sourceType;
    }

    public function getSourceParameterName(): string
    {
        return $this->sourceParameterName;
    }

    public function getSourceReference(): string
    {
        return $this->sourceReference;
    }

    public function setSourceType(Type $sourceType)
    {
        $this->sourceType = $sourceType;
    }

    public function setSourceErrorMessagePart(string $sourceErrorMessagePart): void
    {
        $this->sourceErrorMessagePart = $sourceErrorMessagePart;
    }

    public function getSourceErrorMessagePart(): string
    {
        return $this->sourceErrorMessagePart;
    }

    public function setSourceReference(string $sourceReference): void
    {
        $this->sourceReference = $sourceReference;
    }

    public function getImportTypes(): ?array
    {
        return [];
    }

    public function getThrownTypes(): array
    {
        return [];
    }

    public function setAssignment(Assignment $assignment)
    {
        $this->assignment = $assignment;
    }

    public function isSourceReferenceParameter(): bool
    {
        // TODO: Implement isSourceReferenceParameter() method.
    }

    public function getSourcePresenceCheckerReference(): ?PresenceCheck
    {
        return $this->sourcePresenceCheckerReference;
    }

    public function createUniqueVarName(string $desiredName): string
    {
        $result = Str::getSafeVariableName($desiredName, $this->existingVariableNames);
        $this->existingVariableNames[] = $result;
        return $result;
    }

    public function getSourceLocalVarName(): ?string
    {
        return $this->sourceLocalVarName;
    }

    public function setSourceLocalVarName(?string $sourceLocalVarName)
    {
        $this->sourceLocalVarName = $sourceLocalVarName;
    }

    public function getSourceLoopVarName(): ?string
    {
        return $this->sourceLocalVarName;
    }

    public function setSourceLoopVarName(string $sourceLoopVarName): void
    {
        // TODO: Implement setSourceLoopVarName() method.
    }

    public function getType(): ?AssignmentType
    {
    }

    public function isCallingUpdateMethod(): bool
    {
        return false;
    }

    public function getTemplate(): string
    {
        return 'Common/sourceRHS.twig';
    }
}
