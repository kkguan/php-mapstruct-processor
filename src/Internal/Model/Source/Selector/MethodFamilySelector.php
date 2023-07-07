<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Selector;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Method;

/**
 * Selects those methods from the given input set which match for the requested family of methods: factory methods,
 * lifecycle callback methods, or any other mapping methods.
 */
class MethodFamilySelector implements MethodSelectorInterface
{
    /**
     * @param SelectedMethod[] $candidates
     * @param Type[] $sourceTypes
     */
    public function getMatchingMethods(Method $mappingMethod, array $candidates, array $sourceTypes, Type $mappingTargetType, Type $returnType, SelectionCriteria $criteria)
    {
        $result = [];
        foreach ($candidates as $method) {
            if (
                $method->getMethod()->isObjectFactory() == $criteria->isObjectFactoryRequired()
                && $method->getMethod()->isLifecycleCallbackMethod() == $criteria->isLifecycleCallbackRequired()
                && $method->getMethod()->isPresenceCheck() == $criteria->isPresenceCheckRequired()
            ) {
                $result[] = $method;
            }
        }
        return $result;
    }
}
