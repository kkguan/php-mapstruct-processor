<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Beanmapping;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\TypeFactory;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\SourceMethod;

class MappingReferences
{
    private bool $forForgedMethods;

    /**
     * @param MappingReference[] $mappingReferences
     * @param MappingReference[] $targetThisReferences
     */
    public function __construct(
        private array $mappingReferences,
        private array $targetThisReferences,
        private bool $restrictToDefinedMappings,
    ) {
        $this->forForgedMethods = $restrictToDefinedMappings;
    }

    public static function forSourceMethod(SourceMethod $sourceMethod, Type $targetType, array $targetProperties, TypeFactory $typeFactory): MappingReferences
    {
        $references = [];
        $targetThisReferences = [];
        foreach ($sourceMethod->getOptions()->getMappings() as $mapping) {
            $sourceReference = (new SourceReferenceBuilderFromMapping())
                ->mapping($mapping)
                ->method($sourceMethod)
                ->typeFactory($typeFactory)
                ->build();

            $targetReference = (new TargetReferenceBuilder())
                ->mapping($mapping)
                ->method($sourceMethod)
                ->typeFactory($typeFactory)
                ->targetProperties($targetProperties)
                ->targetType($targetType)
                ->build();

            $mappingReference = new MappingReference($mapping, $targetReference, $sourceReference);

            if (static::isValidWhenInversed($mappingReference)) {
                if ($mapping->getTargetName() == '.') {
                    $targetThisReferences[] = $mappingReference;
                } else {
                    $references[] = $mappingReference;
                }
            }
        }

        return new MappingReferences($references, $targetThisReferences, false);
    }

    public function hasNestedTargetReferences(): bool
    {
        foreach ($this->mappingReferences as $mappingRef) {
            $targetReference = $mappingRef->getTargetReference();
            if ($targetReference && $targetReference->isNested()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return MappingReference[]
     */
    public function getMappingReferences(): array
    {
        return $this->mappingReferences;
    }

    public function isRestrictToDefinedMappings(): bool
    {
        return $this->restrictToDefinedMappings;
    }

    /**
     * @return MappingReference[]
     */
    public function getTargetThisReferences(): array
    {
        return $this->targetThisReferences;
    }

    private static function isValidWhenInversed(MappingReference $mappingReference): bool
    {
        // 未实现功能
        return true;
    }
}
