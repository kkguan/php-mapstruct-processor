<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Selector;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Method;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\SourceMethod;

/**
 * This selector selects a best match based on qualifier annotations.
 * A method is said to be marked with a qualifier annotation if the class in which it resides is annotated with a qualifier annotation or if the method itself is annotated with a qualifier annotation or both.
 * Rules:
 * If no qualifiers are requested in the selection criteria, then only candidate methods without any qualifier annotations remain in the list of potential candidates
 * If multiple qualifiers (qualifedBy) are specified, then all of them need to be present at a candidate for it to match.
 * If no candidate matches the required qualifiers, then all candidates are returned.
 */
class QualifierSelector implements MethodSelectorInterface
{
    public function getMatchingMethods(
        Method $mappingMethod,
        array $candidates,
        array $sourceTypes,
        Type $mappingTargetType,
        Type $returnType,
        SelectionCriteria $criteria
    ) {
        $numberOfQualifiersToMatch = 0;

        // Define some local collections and make sure that they are defined.
        $qualifierTypes = [];
        if (! empty($criteria->getQualifier())) {
            $qualifierTypes = $criteria->getQualifier();
            $numberOfQualifiersToMatch += count($criteria->getQualifier());
        }

        $qualfiedByNames = [];
        if (! empty($criteria->getQualifiedByName())) {
            $qualfiedByNames = $criteria->getQualifiedByName();
            $numberOfQualifiersToMatch += count($criteria->getQualifiedByName());
        }

        // add the mapstruct @Named annotation as annotation to look for
//        if (! empty($qualfiedByNames)) {
//            $qualifierTypes[] = 'Named';
//        }

        // Check there are qualfiers for this mapping: Mapping#qualifier or Mapping#qualfiedByName
        if (empty($qualifierTypes)) {
            // When no qualifiers, disqualify all methods marked with a qualifier by removing them from the candidates
            $nonQualiferAnnotatedMethods = [];
            foreach ($candidates as $candidate) {
                if ($candidate->getMethod() instanceof SourceMethod) {
                    $qualifierAnnotations = $this->getQualifierAnnotationMirrors($candidate->getMethod());
                    if (empty($qualifierAnnotations)) {
                        $nonQualiferAnnotatedMethods[] = $candidate;
                    }
                } else {
                    $nonQualiferAnnotatedMethods[] = $candidate;
                }
            }
            return $nonQualiferAnnotatedMethods;
        }
        // Check all methods marked with qualfier (or methods in Mappers marked wiht a qualfier) for matches.
        $matchingMethods = [];
    }

    private function getQualifierAnnotationMirrors(Method $method)
    {
        // TODO: Implement getQualifierAnnotationMirrors() method.
        // retrieve annotations
        $qualiferAnnotations = [];
    }
}
