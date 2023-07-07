<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Conversion;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Assignment;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\ConversionContext;

interface ConversionProvider
{
    public function to(ConversionContext $conversionContext): Assignment;

    public function from(ConversionContext $conversionContext): Assignment;

    public function getRequiredHelperMethods(ConversionContext $conversionContext): array;

    public function getRequiredHelperFields(ConversionContext $conversionContext): array;
}
