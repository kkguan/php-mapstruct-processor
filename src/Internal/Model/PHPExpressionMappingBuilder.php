<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Assignment\SetterWrapper;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\SourceRHS;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\MappingBuilderBase;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor\AccessorType;

class PHPExpressionMappingBuilder extends MappingBuilderBase
{
    private string $phpExpression;

    public function phpExpression(string $expression): self
    {
        $this->phpExpression = $expression;

        return $this;
    }

    public function build()
    {
        $assignment = new SourceRHS(
            sourceParameterName: '',
            sourceReference: $this->phpExpression,
            sourcePresenceCheckerReference: null,
            sourceType: null,
            existingVariableNames: $this->existingVariableNames,
            sourceErrorMessagePart: ''
        );

        if ($this->targetWriteAccessor->getAccessorType() === AccessorType::SETTER || AccessorType::isFieldAssignment($this->targetWriteAccessor->getAccessorType())) {
            // setter, so wrap in setter
            $assignment = new SetterWrapper(
                rhs: $assignment,
                thrownTypesToExclude: [],
                fieldAssignment: AccessorType::isFieldAssignment($this->targetWriteAccessor->getAccessorType()),
            );
        }
        // target accessor is getter, so wrap the setter in getter map/ collection handling
//            $assignment = new GetterWrapperForCollectionsAndMaps();

        return new PropertyMapping(
            name: $this->targetPropertyName,
            targetWriteAccessorName: $this->targetWriteAccessor->getSimpleName(),
            targetReadAccessorProvider: $this->targetReadAccessor,
            targetType: $this->targetType,
            assignment: $assignment,
            dependsOn: $this->dependsOn,
            defaultValueAssignment: null,
            constructorMapping: $this->targetWriteAccessorType === AccessorType::PARAMETER,
            sourceBeanName: null
        );
    }
}
