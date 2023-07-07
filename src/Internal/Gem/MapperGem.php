<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Gem;

class MapperGem
{
    private function __construct()
    {
    }

    public static function instanceOn(\ReflectionClass $typeElement): MapperGem
    {
        return new static();
    }

    public function getUses()
    {
        // TODO: 没支持
        return [];
    }
}
