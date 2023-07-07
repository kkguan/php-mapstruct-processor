<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Selector;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Method;

/**
 * Selector that tries to resolve an ambiquity between methods that contain source parameters and SourceRHS type parameters.
 */
class SourceRhsSelector implements MethodSelectorInterface
{
    /**
     * @param SelectedMethod[] $candidates
     * @param Type[] $sourceTypes
     */
    public function getMatchingMethods(Method $mappingMethod, array $candidates, array $sourceTypes, Type $mappingTargetType, Type $returnType, SelectionCriteria $criteria)
    {
        if (count($candidates) < 2 || $criteria->getSourceRHS() == null) {
            return $candidates;
        }
        // TODO
    }
}
