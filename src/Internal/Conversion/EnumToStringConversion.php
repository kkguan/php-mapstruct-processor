<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Conversion;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\ConversionContext;

class EnumToStringConversion extends SimpleConversion
{
    protected function getToExpression(ConversionContext $conversionContext): string
    {
        return '<SOURCE>->value';
    }

    protected function getFromExpression(ConversionContext $conversionContext): string
    {
        return "\\{$conversionContext->getTargetType()->getFullyQualifiedName()}::from(<SOURCE>)";
    }
}
