<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Common;

class TypeVarMatcher
{
    public function __construct(
        private TypeFactory $typeFactory,
        private Type $typeToMatch,
    ) {
    }

    public function visit(Type $declared, Type $parameterized)
    {
        $declared->accept($this, $parameterized);
    }
}
