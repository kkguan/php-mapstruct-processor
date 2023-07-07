<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Selector;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\SourceRHS;

class SelectionCriteria
{
    public const PREFER_UPDATE_MAPPING = 1;

    public const OBJECT_FACTORY = 2;

    public const LIFECYCLE_CALLBACK = 3;

    public const PRESENCE_CHECK = 4;

    private array $qualifiers = [];

    private array $qualifiedByNames = [];

    /**
     * @var null
     */
    private $qualifyingResultType;

    private ?SourceRHS $sourceRHS;

    private bool $allowDirect;

    private bool $allowConversion;

    private bool $allowMappingMethod;

    private bool $allow2Steps;

    private $targetPropertyName;

    private $type;

    public function __construct(
        $selectionParameters,
        $mappingControl,
        $targetPropertyName,
        $type
    ) {
        if ($selectionParameters != null) {
        } else {
            $this->qualifyingResultType = null;
            $this->sourceRHS = null;
        }

        if ($mappingControl != null) {
        } else {
            $this->allowDirect = true;
            $this->allowConversion = true;
            $this->allowMappingMethod = true;
            $this->allow2Steps = true;
        }

        $this->targetPropertyName = $targetPropertyName;
        $this->type = $type;
    }

    public static function forMappingMethods(
        $selectionParameters,
        $mappingControl,
        $targetPropertyName,
        bool $preferUpdateMapping
    ): SelectionCriteria {
        return new SelectionCriteria(
            $selectionParameters,
            $mappingControl,
            $targetPropertyName,
            $preferUpdateMapping ? self::PREFER_UPDATE_MAPPING : null
        );
    }

    public function isAllowMappingMethod(): bool
    {
        return $this->allowMappingMethod;
    }

    public function hasQualfiers(): bool
    {
        return ! empty($this->qualifiedByNames) || ! empty($this->qualifiers);
    }

    public function isAllowDirect(): bool
    {
        return $this->allowDirect;
    }

    public function isAllowConversion(): bool
    {
        return $this->allowConversion;
    }

    public function isAllow2Steps(): bool
    {
        return $this->allow2Steps;
    }

    public function isObjectFactoryRequired(): bool
    {
        return $this->type == self::OBJECT_FACTORY;
    }

    public function isLifecycleCallbackRequired(): bool
    {
        return $this->type == self::LIFECYCLE_CALLBACK;
    }

    public function isPresenceCheckRequired(): bool
    {
        return $this->type == self::PRESENCE_CHECK;
    }

    public function getSourceRHS(): ?SourceRHS
    {
        return $this->sourceRHS;
    }

    public function getQualifiers(): array
    {
        return $this->qualifiers;
    }

    public function getQualifiedByNames(): array
    {
        return $this->qualifiedByNames;
    }

    public function getQualifyingResultType()
    {
        return $this->qualifyingResultType;
    }

    public function getTargetPropertyName()
    {
        return $this->targetPropertyName;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getQualifier(): array
    {
        return $this->qualifiers;
    }

    public function getQualifiedByName(): array
    {
        return $this->qualifiedByNames;
    }

    public function isPreferUpdateMapping(): bool
    {
        return $this->type == self::PREFER_UPDATE_MAPPING;
    }

    public function setPreferUpdateMapping(bool $preferUpdateMapping): void
    {
        $this->type = $preferUpdateMapping ? self::PREFER_UPDATE_MAPPING : null;
    }

    public function setIgnoreQualifiers(bool $false)
    {
    }
}
