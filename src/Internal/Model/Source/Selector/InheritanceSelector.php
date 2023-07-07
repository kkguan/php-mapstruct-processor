<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Selector;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Method;

/**
 * Selects on inheritance distance, e.g. the amount of inheritance steps from the parameter type.
 */
class InheritanceSelector implements MethodSelectorInterface
{
    /**
     * @param SelectedMethod[] $candidates
     * @param Type[] $sourceTypes
     */
    public function getMatchingMethods(Method $mappingMethod, array $candidates, array $sourceTypes, Type $mappingTargetType, Type $returnType, SelectionCriteria $criteria)
    {
        if (count($sourceTypes) != 1) {
            return $candidates;
        }

        $singleSourceType = $sourceTypes[0];

        $candidatesWithBestMatchingSourceType = [];
        $bestMatchingSourceTypeDistance = PHP_INT_MAX;

        // find the methods with the minimum distance regarding getParameter getParameter type
        foreach ($candidates as $method) {
            // TODO: check if this is correct
            $singleSourceParam = $method->getMethod()->getParameters()[0];

            $sourceTypeDistance = $singleSourceParam->getType()->getDistanceTo($singleSourceType);
        }

        return $candidatesWithBestMatchingSourceType;
    }
}
