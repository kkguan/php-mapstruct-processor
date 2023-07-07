<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Conversion;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\ConversionContext;

class FloatToIntConversion extends SimpleConversion
{
    protected function getToExpression(ConversionContext $conversionContext): string
    {
        return 'intvar(<SOURCE>)';
    }

    protected function getFromExpression(ConversionContext $conversionContext): string
    {
        return 'floatval(<SOURCE>)';
    }
}
