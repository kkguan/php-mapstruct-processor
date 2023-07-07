<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor;

interface ReadAccessor extends Accessor
{
    public function getReadValueSource(): string;
}
