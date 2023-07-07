<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;

class ForgedMethodHistory
{
    public function __construct(
        ?ForgedMethodHistory $history,
        string $sourceElement,
        string $targetPropertyName,
        Type $sourceType,
        Type $targetType,
        bool $usePropertyNames,
        string $elementType
    ) {
        $this->prevHistory = $history;
        $this->sourceElement = $sourceElement;
        $this->targetPropertyName = $targetPropertyName;
        $this->sourceType = $sourceType;
        $this->targetType = $targetType;
        $this->usePropertyNames = $usePropertyNames;
        $this->elementType = $elementType;
    }
}
