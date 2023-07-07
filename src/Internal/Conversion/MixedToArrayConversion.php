<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Conversion;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\ConversionContext;

class MixedToArrayConversion extends SimpleConversion
{
    protected function getToExpression(ConversionContext $conversionContext): string
    {
        return '\Kkguan\PHPMapstruct\Processor\Internal\Util\Arr::mixedToArray(<SOURCE>)';
    }

    protected function getFromExpression(ConversionContext $conversionContext): string
    {
        return '<SOURCE>';
    }
}
