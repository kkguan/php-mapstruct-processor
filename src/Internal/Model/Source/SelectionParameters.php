<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Source;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\SourceRHS;

class SelectionParameters
{
    private function __construct(
        private array $qualifiers,
        private array $qualifyingNames,
        private array $conditionQualifiers,
        private array $conditionQualifyingNames,
        private $resultType,
        private $typeUtils,
        private SourceRHS $sourceRHS
    ) {
    }

    public static function forSourceRHS(SourceRHS $sourceRHS): SelectionParameters
    {
        return new SelectionParameters(
            qualifiers: [],
            qualifyingNames: [],
            conditionQualifiers: [],
            conditionQualifyingNames: [],
            resultType: null,
            typeUtils: null,
            sourceRHS: $sourceRHS
        );
    }
}
