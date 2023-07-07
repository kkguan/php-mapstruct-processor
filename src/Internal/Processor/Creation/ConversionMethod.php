<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Processor\Creation;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Assignment;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Method;

class ConversionMethod
{
    private bool $hasResult = false;

    private ?Assignment $result = null;

    /**
     * @param Method[] $methods
     */
    public function __construct(
        private ?ResolvingAttempt $attempt = null,
        private array $methods = [],
        private null|\Closure $create = null,
    ) {
    }

    public static function getBestMatch(ResolvingAttempt $attempt, Type $sourceType, Type $targetType)
    {
        // TODO: 暂时只支持内置类型的转换
        $mAttempt = (new self($attempt, $attempt->getMethods(), function (Method $method) use ($attempt) {
            return $attempt->toMethodRef($method);
        }))->getBestMatch2($sourceType, $targetType);

        if ($mAttempt->isHasResult()) {
            return $mAttempt->getResult();
        }

        $mAttempt = (new self($attempt, $attempt->builtIns(), function (Method $method) use ($attempt) {
            return $attempt->toMethodRef($method);
        }))->getBestMatch2($sourceType, $targetType);

        if ($mAttempt->isHasResult()) {
            return $mAttempt->getResult();
        }
    }

    public function isHasResult(): bool
    {
        return $this->hasResult;
    }

    public function setHasResult(bool $hasResult): ConversionMethod
    {
        $this->hasResult = $hasResult;
        return $this;
    }

    public function getResult(): ?Assignment
    {
        return $this->result;
    }

    public function setResult(?Assignment $result): ConversionMethod
    {
        $this->result = $result;
        return $this;
    }

    private function getBestMatch2(Type $sourceType, Type $targetType)
    {
        $xRefCandidate = $this->attempt->resolveViaConversion($sourceType, $targetType);
        if (empty($xRefCandidate)) {
            return null;
        }

        $this->hasResult = true;

//        dump($xRefCandidate->getAssignment()->getSourceReference());
//        dd(__METHOD__ . '::' . __LINE__);

//        $yCandidates = $xRefCandidates = [];
//
//        foreach ($this->methods as $yCandidate) {
//            $ySourceType = $yCandidate->getMappingSourceType();
//            $ySourceType = $ySourceType->resolveParameterToType($targetType, $yCandidate->getResultType())->getMatch();
//            $yTargetType = $yCandidate->getResultType();
//
//            if (empty($ySourceType) || ! $yTargetType->isRawAssignableTo($targetType)) {
//                continue;
//            }
//
//            $xRefCandidate = $this->attempt->resolveViaConversion($sourceType, $ySourceType);
//        }

        return $this;
    }
}
