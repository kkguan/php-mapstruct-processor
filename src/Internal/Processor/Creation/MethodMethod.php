<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Processor\Creation;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Assignment;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\SourceRHS;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Method;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Selector\SelectedMethod;

class MethodMethod
{
    private bool $hasResult = false;

    private ?Assignment $result = null;

    /**
     * @param Method[] $yMethod
     * @param Method[] $xMethod
     */
    public function __construct(
        private ?ResolvingAttempt $attempt = null,
        private array $xMethod = [],
        private array $yMethod = [],
        private null|\Closure $xCreate = null,
        private null|\Closure $yCreate = null
    ) {
    }

    public static function getBestMatch(ResolvingAttempt $att, Type $sourceType, Type $targetType): ?Assignment
    {
        // 如果是PHP内置类型转内置类型，直接 new Assignment 返回
        dd($att->getSourceRHS());

//        if ($sourceType->getTypeElement()->isBuiltin() && $targetType->getTypeElement()->isBuiltin()) {
//            $sourceRhs =  new SourceRHS($sourceType, $targetType, $sourceType->getName());
//        }

        // TODO
//        $xCreate = function (SelectedMethod $selectedMethod) use ($att) {
//            return $att->toMethodRef($selectedMethod);
//        };
//
//        $yCreate = function (SelectedMethod $selectedMethod) use ($att) {
//            return $att->toMethodRef($selectedMethod);
//        };
//
//        $mmAttempt = (new self($att, $att->getMethods(), $att->getMethods(), $xCreate, $yCreate))->getBestMatch2($sourceType, $targetType);
//        if ($mmAttempt->isHasResult()) {
//            return $mmAttempt->getResult();
//        }
//
//        if ($att->hasQualfiers()) {
//            $mmAttempt->getBestMatchIgnoringQualifiersBeforeY($sourceType, $targetType);
//            if ($mmAttempt->isHasResult()) {
//                return $mmAttempt->getResult();
//            }
//        }
    }

    public function getBestMatch2(Type $sourceType, Type $targetType): static
    {
        return $this->getBestMatch($sourceType, $targetType, null);
    }

    public function getBestMatch3(Type $sourceType, Type $targetType, ?BestMatchType $matchType): static
    {
        $yCandidates = [];
        $xCandidates = [];

        // Iterate over all source methods. Check if the return type matches with the parameter that we need.
        // so assume we need a method from A to C we look for a methodX from A to B (all methods in the
        // list form such a candidate).
        // For each of the candidates, we need to look if there's a methodY, either
        // sourceMethod or builtIn that fits the signature B to C. Only then there is a match. If we have a match
        // a nested method call can be called. so C = methodY( methodX (A) )
        $this->attempt->getSelectionCriteria()->setPreferUpdateMapping(false);
        $this->attempt->getSelectionCriteria()->setIgnoreQualifiers($matchType?->getValue() == BestMatchType::IGNORE_QUALIFIERS_BEFORE_Y_CANDIDATES);

        /** @var Method $yCandidate */
        foreach ($this->yMethods as $yCandidate);
        // TODO: 找相同类型匹配的方法，理论上php没有多态可不实现

        $this->attempt->getSelectionCriteria()->setPreferUpdateMapping(true);
        $this->attempt->getSelectionCriteria()->setIgnoreQualifiers($matchType?->getValue() == BestMatchType::IGNORE_QUALIFIERS_AFTER_Y_CANDIDATES);

        // collect all results

        return $this;
    }

    public function isHasResult(): bool
    {
        return $this->hasResult;
    }

    public function getResult(): ?Assignment
    {
        return $this->result;
    }

    private function getBestMatchIgnoringQualifiersBeforeY(Type $sourceType, Type $targetType)
    {
        return $this->getBestMatch3($sourceType, $targetType, new BestMatchType(BestMatchType::IGNORE_QUALIFIERS_BEFORE_Y_CANDIDATES));
    }
}
