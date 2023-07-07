<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Gem;

use Kkguan\PHPMapstruct\MappingTarget;

class MappingTargetGem
{
    private bool $isValid;

    private \ReflectionAttribute $element;

    public function __construct(MappingTargetGemBuilder $builder)
    {
        $this->isValid = true;
        $this->element = $builder->getElement();
    }

    public static function instanceOn(\ReflectionParameter $element): ?MappingTargetGemBuilder
    {
        $attributes = $element->getAttributes(MappingTarget::class);
        $element = $attributes[0] ?? null;

        return static::build($element, new MappingTargetGemBuilder());
    }

    public static function build(?\ReflectionAttribute $element, ?MappingTargetGemBuilder $builder): ?MappingTargetGemBuilder
    {
        if ($element == null || $builder == null) {
            return null;
        }
        $builder->setElement($element);
        return $builder;
    }
}
