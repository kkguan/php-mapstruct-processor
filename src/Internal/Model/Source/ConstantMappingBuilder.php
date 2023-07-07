<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Source;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\FormattingParameters;

class ConstantMappingBuilder extends MappingBuilderBase
{
    private ?string $constantExpression;

    private ?FormattingParameters $formattingParameters;

    // TODO
//    private MappingControl $mappingControl;

    private SelectionParameters $selectionParameters;

    public function getConstantExpression(): ?string
    {
        return $this->constantExpression;
    }

    public function setConstantExpression(?string $constantExpression): ConstantMappingBuilder
    {
        $this->constantExpression = $constantExpression;
        return $this;
    }

    public function getFormattingParameters(): ?FormattingParameters
    {
        return $this->formattingParameters;
    }

    public function setFormattingParameters(?FormattingParameters $formattingParameters): ConstantMappingBuilder
    {
        $this->formattingParameters = $formattingParameters;
        return $this;
    }

    public function getSelectionParameters(): SelectionParameters
    {
        return $this->selectionParameters;
    }

    public function setSelectionParameters(SelectionParameters $selectionParameters): ConstantMappingBuilder
    {
        $this->selectionParameters = $selectionParameters;
        return $this;
    }

    public function build()
    {
        $sourceErrorMessagePart = sprintf('constant \'%s\'', $this->constantExpression);
        $errorMessageDetails = null;

        $baseForLiteral = null;
    }
}
