<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Selector;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\TypeFactory;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Method;

/**
 * This selector selects a best match based on the result type.
 * Suppose: Sedan -> Car -> Vehicle, MotorCycle -> Vehicle By means of this selector one can pinpoint the exact desired return type (Sedan, Car, MotorCycle, Vehicle).
 */
class TargetTypeSelector implements MethodSelectorInterface
{
    private TypeFactory $typeFactory;

    public function __construct(TypeFactory $typeFactory)
    {
        $this->typeFactory = $typeFactory;
    }

    /**
     * @param SelectedMethod[] $candidates
     * @param Type[] $sourceTypes
     */
    public function getMatchingMethods(Method $mappingMethod, array $candidates, array $sourceTypes, Type $target, Type $target1, SelectionCriteria $criteria)
    {
        $qualifyingTypeMirror = $criteria->getQualifyingResultType();

        if ($qualifyingTypeMirror != null && ! $criteria->isLifecycleCallbackRequired()) {
            // TODO
        } else {
            return $candidates;
        }
    }
}
