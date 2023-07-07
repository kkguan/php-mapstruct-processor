<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Conversion;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Assignment;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\ConversionContext;
use Kkguan\PHPMapstruct\Processor\Internal\Model\TypeConversion;

abstract class SimpleConversion extends AbstractConversionProvider
{
    public function to(ConversionContext $conversionContext): Assignment
    {
        $toExpression = $this->getToExpression($conversionContext);
        return new TypeConversion(
            $this->getToConversionImportTypes($conversionContext),
            $this->getToConversionExceptionTypes($conversionContext),
            $toExpression
        );
    }

    public function from(ConversionContext $conversionContext): Assignment
    {
        $fromExpression = $this->getFromExpression($conversionContext);
        return new TypeConversion(
            $this->getFromConversionImportTypes($conversionContext),
            $this->getFromConversionExceptionTypes($conversionContext),
            $fromExpression
        );
    }

    public function getRequiredHelperMethods(ConversionContext $conversionContext): array
    {
        return [];
    }

    abstract protected function getToExpression(ConversionContext $conversionContext): string;

    abstract protected function getFromExpression(ConversionContext $conversionContext): string;

    protected function getToConversionImportTypes(ConversionContext $conversionContext): array
    {
        return [];
    }

    protected function getToConversionExceptionTypes(ConversionContext $conversionContext): array
    {
        return [];
    }

    private function getFromConversionImportTypes(ConversionContext $conversionContext)
    {
        return [];
    }

    private function getFromConversionExceptionTypes(ConversionContext $conversionContext)
    {
        return [];
    }
}
