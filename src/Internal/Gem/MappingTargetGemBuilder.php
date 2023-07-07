<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Gem;

class MappingTargetGemBuilder
{
    private \ReflectionAttribute $element;

    public function setElement(\ReflectionAttribute $element)
    {
        $this->element = $element;
    }

    public function getElement(): \ReflectionAttribute
    {
        return $this->element;
    }

    public function build(): MappingTargetGem
    {
        return new MappingTargetGem($this);
    }
}
