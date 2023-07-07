<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Conversion;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\ConversionContext;

class IntegerToBoolConversion extends SimpleConversion
{
    protected function getToExpression(ConversionContext $conversionContext): string
    {
        return '(bool) <SOURCE>';
    }

    protected function getFromExpression(ConversionContext $conversionContext): string
    {
        return '(int) <SOURCE>';
    }
}
