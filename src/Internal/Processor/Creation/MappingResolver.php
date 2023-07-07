<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Processor\Creation;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Assignment;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\SourceRHS;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\TypeFactory;
use Kkguan\PHPMapstruct\Processor\Internal\Model\ForgedMethodHistory;
use Kkguan\PHPMapstruct\Processor\Internal\Model\MapperReference;
use Kkguan\PHPMapstruct\Processor\Internal\Model\MethodReference;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Builtin\BuiltInMappingMethods;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Method;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Selector\MethodSelectors;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Selector\SelectedMethod;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Selector\SelectionCriteria;
use Psr\Log\LoggerInterface;

class MappingResolver
{
    private BuiltInMappingMethods $builtInMethods;

    private array $usedSupportedMappings = [];

    private array $usedSupportedFields = [];

    /**
     * @param MapperReference[] $mapperReferences
     */
    public function __construct(
        private TypeFactory $typeFactory,
        private array $sourceModel,
        private array $mapperReferences,
        private bool $verboseLogging,
        private LoggerInterface $messager,
    ) {
        $this->builtInMethods = new BuiltInMappingMethods($typeFactory);
        $this->methodSelectors = new MethodSelectors($typeFactory);
    }

    public function getTargetAssignment(
        Method $mappingMethod,
        ForgedMethodHistory $description,
        Type $targetType,
        $formattingParameters,
        SelectionCriteria $criteria,
        SourceRHS $sourceRHS,
        $positionHint,
        $forger
    ): ?Assignment {
        $attempt = new ResolvingAttempt(
            $this->sourceModel,
            $mappingMethod,
            $description,
            $formattingParameters,
            $sourceRHS,
            $criteria,
            $positionHint,
            $forger,
            $this->builtInMethods->getBuiltInMethods(),
            $this->methodSelectors,
            mappingResolver: $this
        );
        return $attempt->getTargetAssignment($sourceRHS->getSourceTypeForMatching(), $targetType);
    }

    public function getUsedSupportedMappings()
    {
        return $this->usedSupportedMappings;
    }

    public function getUsedSupportedFields()
    {
        return $this->usedSupportedFields;
    }

    public function toMethodRef(SelectedMethod $selectedMethod)
    {
        // TODO: 未完成toMethodRef
        $mapperReference = $this->findMapperReference($selectedMethod->getMethod());
        return MethodReference::forMapperReference($selectedMethod->getMethod(), $mapperReference, $selectedMethod->getParameterBindings());
    }

    public function findMapperReference(Method $method): ?MapperReference
    {
        // TODO: 找到 mapper 的引用
        /* @var MapperReference $ref */
        foreach ($this->mapperReferences as $ref) {
            if ($ref->getType() === $method->getReturnType()->getName()) {
                $ref->setUsed($ref->isUsed() || ! $method->isStatic());
                $ref->setTypeRequiresImport(true);
                return $ref;
            }
        }

        return null;
    }

    public function getTypeFactory(): TypeFactory
    {
        return $this->typeFactory;
    }

    public function getMessager(): LoggerInterface
    {
        return $this->messager;
    }

    public function getMapperReferences(): array
    {
        return $this->mapperReferences;
    }
}
