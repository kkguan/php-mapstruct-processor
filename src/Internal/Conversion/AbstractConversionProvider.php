<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Conversion;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\ConversionContext;

abstract class AbstractConversionProvider implements ConversionProvider
{
    public function getRequiredHelperFields(ConversionContext $conversionContext): array
    {
        return [];
    }
}
