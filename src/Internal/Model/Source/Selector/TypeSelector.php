<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Selector;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Parameter;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\ParameterBinding;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\SourceRHS;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\TypeFactory;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Method;

class TypeSelector implements MethodSelectorInterface
{
    public function __construct(private TypeFactory $typeFactory)
    {
    }

    /**
     * @param SelectedMethod[] $candidates
     * @param Type[] $sourceTypes
     */
    public function getMatchingMethods(Method $mappingMethod, array $candidates, array $sourceTypes, Type $mappingTargetType, Type $returnType, SelectionCriteria $criteria)
    {
        if (empty($candidates)) {
            return $candidates;
        }

        $result = [];
        $availableBindings = [];
        if (empty($sourceTypes)) {
            // if no source types are given, we have a factory or lifecycle method
            $availableBindings[] = $this->getAvailableParameterBindingsFromMethod($mappingMethod, $mappingTargetType, $criteria->getSourceRHS());
        } else {
            $availableBindings = $this->getAvailableParameterBindingsFromSourceTypes($mappingMethod, $sourceTypes, $mappingTargetType, $criteria->getSourceRHS());
        }

        foreach ($candidates as $method) {
            $parameterBindingPermutations = $this->getCandidateParameterBindingPermutations($availableBindings, $method->getMethod()->getParameters());

            if ($parameterBindingPermutations !== null) {
                $matchingMethod = $this->getMatchingParameterBinding($returnType, $mappingMethod, $method, $parameterBindingPermutations);
            }
        }

        return $result;
    }

    private function getAvailableParameterBindingsFromMethod(Method $method, Type $targetType, ?SourceRHS $sourceRHS)
    {
        $availableParams = [];

        if (! empty($sourceRHS)) {
            $availableParams[] = ParameterBinding::fromParameters($method->getParameters());
            $availableParams[] = ParameterBinding::forSourceRHS($sourceRHS);
        } else {
            $availableParams[] = ParameterBinding::fromParameters($method->getParameters());
        }

        return array_merge($availableParams, $this->addMappingTargetAndTargetTypeBindings($availableParams, $targetType));
    }

    /**
     * Adds default parameter bindings for the mapping-target and target-type if not already available.
     */
    private function addMappingTargetAndTargetTypeBindings(array $availableParams, Type $targetType)
    {
        $mappingTargetAvailable = false;
        $targetTypeAvailable = false;

        // search available parameter bindings if mapping-target and/or target-type is available
        foreach ($availableParams as $availableParam) {
            if ($availableParam->isMappingTarget()) {
                $mappingTargetAvailable = true;
            }
            if ($availableParam->isTargetType()) {
                $targetTypeAvailable = true;
            }
        }

        if (! $mappingTargetAvailable) {
            $availableParams[] = ParameterBinding::forMappingTargetBinding($targetType);
        }
        if (! $targetTypeAvailable) {
            $availableParams[] = ParameterBinding::forTargetTypeBinding($targetType);
        }

        return $availableParams;
    }

    /**
     * @param Type[] $sourceTypes
     * @return ParameterBinding[]
     */
    private function getAvailableParameterBindingsFromSourceTypes(Method $mappingMethod, array $sourceTypes, Type $targetType, ?SourceRHS $sourceRHS): array
    {
        $availableParams = [];

        foreach ($sourceTypes as $sourceType) {
            $availableParams[] = ParameterBinding::forSourceTypeBinding($sourceType);
        }

        foreach ($mappingMethod->getParameters() as $parameter) {
            if ($parameter->isMappingTarget()) {
                $availableParams[] = ParameterBinding::fromParameter($parameter);
            }
        }

        return array_merge($availableParams, $this->addMappingTargetAndTargetTypeBindings($availableParams, $targetType));
    }

    private function getCandidateParameterBindingPermutations(array $availableBindings, array $methodParameters): ?array
    {
        $bindingPermutations = [[]];

        if (count($methodParameters) > count($availableBindings)) {
            return null;
        }

        $bindingPermutations = count($methodParameters);

        foreach ($methodParameters as $parameter) {
            $candidateBindings = $this->findCandidateBindingsForParameter($availableBindings, $parameter);

            if (empty($candidateBindings)) {
                return null;
            }

            if (count($candidateBindings) == 1) {
                // short-cut to avoid list-copies for the usual case where only one binding fits
                foreach ($bindingPermutations as &$permutation) {
                    $permutation[] = $candidateBindings[0];
                }
            } else {
                $newPermutations = [];
                foreach ($bindingPermutations as $permutation) {
                    // create a copy of each variant for each binding
                    foreach ($candidateBindings as $candidateBinding) {
                        $newPermutation = $permutation;
                        $newPermutation[] = $candidateBinding;
                        $newPermutations[] = $newPermutation;
                    }
                }
                $bindingPermutations = $newPermutations;
            }
        }

        return $bindingPermutations;
    }

    /**
     * Params:
     * candidateParameters – available for assignment. parameter – that need assignment from one of the candidate parameter bindings.
     * Returns:
     * list of candidate parameter bindings that might be assignable.
     * @param ParameterBinding[] $candidateParameters
     * @return ParameterBinding[]
     */
    private function findCandidateBindingsForParameter(array $candidateParameters, Parameter $parameter): array
    {
        $candidateBindings = [];

        foreach ($candidateParameters as $candidateParameter) {
            if (
                $candidateParameter->isMappingTarget() == $parameter->isMappingTarget()
                && $candidateParameter->isTargetType() == $parameter->isTargetType()
                && $candidateParameter->isMappingContext() == $parameter->isMappingContext()
            ) {
                $candidateBindings[] = $candidateParameter;
            }
        }

        return $candidateBindings;
    }

    /**
     * @param array<array<ParameterBinding>> $parameterAssignmentVariants
     */
    private function getMatchingParameterBinding(Type $returnType, Method $method, SelectedMethod $selectedMethodInfo, array $parameterAssignmentVariants)
    {
        $matchingParameterAssignmentVariants = $parameterAssignmentVariants;
        $selectedMethod = $selectedMethodInfo->getMethod();

        // remove all assignment variants that doesn't match the types from the method
        foreach ($matchingParameterAssignmentVariants as $index => $parameterAssignmentVariant) {
            foreach ($parameterAssignmentVariant as $parameterAssignment) {
                if ($parameterAssignment->getType() !== $returnType) {
                    unset($matchingParameterAssignmentVariants[$index]);
                }
            }
        }

        if (empty($matchingParameterAssignmentVariants)) {
            return null;
        }
    }
}
