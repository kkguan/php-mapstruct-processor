<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Conversion;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Assignment;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\ConversionContext;

class ReverseConversion implements ConversionProvider
{
    public function __construct(private ConversionProvider $conversionProvider)
    {
    }

    public function to(ConversionContext $conversionContext): Assignment
    {
        return $this->conversionProvider->from($conversionContext);
    }

    public function from(ConversionContext $conversionContext): Assignment
    {
        return $this->conversionProvider->to($conversionContext);
    }

    public function getRequiredHelperMethods(ConversionContext $conversionContext): array
    {
        return $this->conversionProvider->getRequiredHelperMethods($conversionContext);
    }

    public function getRequiredHelperFields(ConversionContext $conversionContext): array
    {
        return $this->conversionProvider->getRequiredHelperFields($conversionContext);
    }
}
