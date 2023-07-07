<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Conversion;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\ConversionContext;

class IntegerToFloatConversion extends SimpleConversion
{
    public function getToExpression(ConversionContext $conversionContext): string
    {
        return '(float) <SOURCE>';
    }

    public function getFromExpression(ConversionContext $conversionContext): string
    {
        return '(int) <SOURCE>';
    }
}
