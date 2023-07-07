<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Processor\Creation;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Assignment;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Method;

class ConversionType
{
    private bool $hasResult = false;

    private ?Assignment $result = null;

    /**
     * @param Method[] $methods
     */
    public function __construct(
        private ?ResolvingAttempt $attempt = null,
        private array $methods = []
    ) {
    }

    public static function getBestMatch(ResolvingAttempt $attempt, Type $sourceType, Type $targetType): ?Assignment
    {
//        $attempt->getSourceRHS();
        $xRefCandidate = $attempt->resolveViaConversion($sourceType, $targetType);

        if (empty($xRefCandidate)) {
            return null;
        }

        $assignment = $xRefCandidate->getAssignment();
        $assignment->setAssignment($attempt->getSourceRHS());

        return $assignment;
    }
}
