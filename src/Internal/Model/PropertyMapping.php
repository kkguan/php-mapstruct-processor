<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Assignment;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\ModelElement;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor\ReadAccessor;

class PropertyMapping extends ModelElement
{
    public function __construct(
        private string $name,
        private ?string $sourceBeanName,
        private string $targetWriteAccessorName,
        private ReadAccessor $targetReadAccessorProvider,
        private Type $targetType,
        private ?Assignment $assignment,
        private array $dependsOn,
        private $defaultValueAssignment,
        private bool $constructorMapping,
    ) {
//        dump(
//            $name,
//            $sourceBeanName,
//            $targetWriteAccessorName,
//            $targetReadAccessorProvider,
//            $targetType,
//            $assignment,
//            $dependsOn,
//            $defaultValueAssignment,
//            $constructorMapping
//        );
    }

    public function isConstructorMapping(): bool
    {
        return $this->constructorMapping;
    }

    public function getSourceBeanName(): string
    {
        return $this->sourceBeanName;
    }

    public function getImportTypes(): array
    {
        // TODO: Implement getImportTypes() method.
    }

    public function getAssignment()
    {
        return $this->assignment;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTargetWriteAccessorName(): string
    {
        return $this->targetWriteAccessorName;
    }

    public function getTargetReadAccessorProvider(): ReadAccessor
    {
        return $this->targetReadAccessorProvider;
    }

    public function getTargetType(): Type
    {
        return $this->targetType;
    }

    public function getDependsOn(): array
    {
        return $this->dependsOn;
    }

    public function getDefaultValueAssignment()
    {
        return $this->defaultValueAssignment;
    }

    public function getTemplate(): string
    {
        return 'propertyMapping.twig';
    }
}
