<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Beanmapping;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Parameter;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\TypeFactory;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\MappingOptions;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\SourceMethod;

class SourceReferenceBuilderFromMapping
{
    private string $sourceName;

    private bool $isForwarded = false;

    private SourceMethod $method;

    private TypeFactory $typeFactory;

    public function mapping(MappingOptions $mapping)
    {
        $this->sourceName = $mapping->getSourceName();
        return $this;
    }

    public function method(SourceMethod $method)
    {
        $this->method = $method;
        return $this;
    }

    public function typeFactory(TypeFactory $typeFactory)
    {
        $this->typeFactory = $typeFactory;
        return $this;
    }

    public function build(): ?SourceReference
    {
        if (empty($this->sourceName)) {
            return null;
        }

        $sourceNameTrimmed = trim($this->sourceName);
        $segments = explode('.', $sourceNameTrimmed);

        if (count($this->method->getSourceParameters()) > 1) {
            $parameter = $this->fetchMatchingParameterFromFirstSegment($segments);
            if ($parameter) {
                $result = $this->buildFromMultipleSourceParameters($segments, $parameter);
            }
        } else {
            $parameter = $this->method->getSourceParameters()[0];
            $result = $this->buildFromSingleSourceParameters($segments, $parameter);
        }

        return $result;
    }

    private function fetchMatchingParameterFromFirstSegment(array $segments): ?Parameter
    {
        $parameter = null;
        if (count($segments) > 0) {
            $parameterName = $segments[0];
            $parameter = $this->getSourceParameterFromMethodOrTemplate($parameterName);
            if ($parameter === null) {
                $sourceParameters = '';
                foreach ($this->method->getSourceParameters() as $index => $parameter) {
                    $index > 0 && $sourceParameters .= ',';
                    $sourceParameters .= $parameter->getName();
                }
                throw new \InvalidArgumentException(sprintf('Method has no source parameter named "%s". Method source parameters are: "%s".', $parameterName, $sourceParameters));
            }
        }

        return $parameter;
    }

    private function getSourceParameterFromMethodOrTemplate(string $parameterName): ?Parameter
    {
        $result = null;
        if ($this->isForwarded) {
            // TODO
        } else {
            $result = Parameter::getSourceParameter($this->method->getParameters(), $parameterName);
        }

        return $result;
    }

    private function buildFromSingleSourceParameters(array $segments, Parameter $parameter): SourceReference
    {
        $propertyNames = $segments;
        $entries = $this->matchWithSourceAccessorTypes($parameter->getType(), $propertyNames);
        $foundEntryMatch = (count($entries) == count($propertyNames));
        if (! $foundEntryMatch) {
            if ($this->getSourceParameterFromMethodOrTemplate($segments[0]) != null) {
                $propertyNames = array_slice($segments, 1);
                $entries = $this->matchWithSourceAccessorTypes($parameter->getType(), $propertyNames);
                $foundEntryMatch = (count($entries) == count($propertyNames));
            } else {
                $parameter = null;
            }
        }

        if (! $foundEntryMatch) {
            // TODO:log
        }

        return new SourceReference(
            sourceParameter: $parameter,
            sourcePropertyEntries: $entries,
            isValid: $foundEntryMatch,
        );
    }

    private function buildFromMultipleSourceParameters(array $segments, ?Parameter $parameter): SourceReference
    {
        $foundEntryMatch = false;

        $entries = [];
        if (count($segments) > 1 && $parameter != null) {
            $propertyNames = array_slice($segments, 1);
            $entries = $this->matchWithSourceAccessorTypes($parameter->getType(), $propertyNames);
            $foundEntryMatch = (count($entries) == count($propertyNames));
        } else {
            $foundEntryMatch = true;
        }

        if (! $foundEntryMatch) {
            // TODO:log
        }

        return new SourceReference(
            sourceParameter: $parameter,
            sourcePropertyEntries: $entries,
            isValid: $foundEntryMatch,
        );
    }

    /**
     * @param mixed $entryNames
     * @return PropertyEntry[]
     * @throws \ReflectionException
     */
    private function matchWithSourceAccessorTypes(?Type $type, $entryNames): array
    {
        $sourceEntries = [];
        $newType = $type;
        foreach ($entryNames as $i => $entryName) {
            $matchFound = false;
            $noBoundsType = $newType->withoutBounds();
            $readAccessor = $noBoundsType->getReadAccessor($entryName);
            if ($readAccessor !== null) {
                $presenceChecker = $noBoundsType->getPresenceChecker($entryName);
                $newType = $this->typeFactory->getReturnTypeByAccessor(new \ReflectionClass($noBoundsType->getFullyQualifiedName()), $readAccessor);
                $sourceEntries[] = PropertyEntry::forSourceReference(
                    array_slice($entryNames, $i, $i + 1),
                    $readAccessor,
                    $presenceChecker,
                    $newType,
                );
                $matchFound = true;
            }

            if (! $matchFound) {
                break;
            }
        }

        return $sourceEntries;
    }
}
