<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Selector;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Method;

class CreateOrUpdateSelector implements MethodSelectorInterface
{
    /**
     * @param SelectedMethod[] $candidates
     * @param Type[] $sourceTypes
     */
    public function getMatchingMethods(Method $mappingMethod, array $candidates, array $sourceTypes, Type $mappingTargetType, Type $returnType, SelectionCriteria $criteria)
    {
        if ($criteria->isLifecycleCallbackRequired() || $criteria->isObjectFactoryRequired() || $criteria->isPresenceCheckRequired()) {
            return $candidates;
        }

        $createCandidates = [];
        $updateCandidates = [];

        foreach ($candidates as $method) {
            $isCreateCandidate = $method->getMethod()->getMappingTargetParameter() == null;
            if ($isCreateCandidate) {
                $createCandidates[] = $method;
            } else {
                $updateCandidates[] = $method;
            }
        }

        if ($criteria->isPreferUpdateMapping() && ! empty($updateCandidates)) {
            return $updateCandidates;
        }
        return $createCandidates;
    }
}
