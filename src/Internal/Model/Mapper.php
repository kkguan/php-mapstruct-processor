<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model;

class Mapper extends GeneratedType
{
    private MapperBuilder $builder;

    private \ReflectionClass $element;

    public function builder(): MapperBuilder
    {
        return $this->builder = (new MapperBuilder());
    }

    public function setElement(\ReflectionClass $element): Mapper
    {
        $this->element = $element;
        return $this;
    }

    public function getBuilder(): MapperBuilder
    {
        return $this->builder;
    }

    public function setBuilder(MapperBuilder $builder): Mapper
    {
        $this->builder = $builder;
        return $this;
    }
}
