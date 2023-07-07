<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Common;

interface ConversionContext
{
    public function getTargetType(): Type;

    public function getDateFormat(): ?string;

    public function getNumberFormat(): ?string;

    public function getTypeFactory(): TypeFactory;
}
