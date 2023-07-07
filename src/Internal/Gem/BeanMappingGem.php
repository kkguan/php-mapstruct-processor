<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Gem;

use Kkguan\PHPMapstruct\Processor\Shaded\Tools\Gem\Gem;

class BeanMappingGem implements Gem
{
    private function __construct()
    {
    }

    public static function instanceOn(\ReflectionMethod $method): ?BeanMappingGem
    {
        return null;
    }

    public function mirror()
    {
        // TODO: Implement mirror() method.
    }

    public function isValid(): bool
    {
        // TODO: Implement isValid() method.
    }
}
