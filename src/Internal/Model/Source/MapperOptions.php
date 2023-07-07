<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Source;

use Kkguan\PHPMapstruct\Processor\Internal\Gem\MapperGem;
use Kkguan\PHPMapstruct\Processor\Internal\Option\Options;

class MapperOptions extends DelegatingOptions
{
    private function __construct(private MapperGem $mapper, $mapperConfigTypeDelegating, DelegatingOptions $next)
    {
        parent::__construct($next);
    }

    public static function getInstanceOn(\ReflectionClass $typeElement, Options $options): static
    {
        /** @var MapperGem $mapper */
        $mapper = MapperGem::instanceOn($typeElement);
        $defaults = new DefaultOptions($mapper, $options);
        return new MapperOptions(
            mapper: $mapper,
            mapperConfigTypeDelegating: null,
            next: $defaults
        );
    }

    public function uses(): array
    {
        return $this->mapper->getUses();
    }
}
