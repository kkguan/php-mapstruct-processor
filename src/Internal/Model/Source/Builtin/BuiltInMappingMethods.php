<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Builtin;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\TypeFactory;

class BuiltInMappingMethods
{
    private array $builtInMethods = [];

    public function __construct(TypeFactory $typeFactory)
    {
    }

    public function getBuiltInMethods()
    {
        return $this->builtInMethods;
    }
}
