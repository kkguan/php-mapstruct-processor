<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Source;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;

abstract class AbstractMethod implements Method
{
    public function getMappingSourceType(): ?Type
    {
        return $this->getSourceParameters()[0]->getType();
    }
}
