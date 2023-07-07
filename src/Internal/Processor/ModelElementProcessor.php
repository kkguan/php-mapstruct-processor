<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Processor;

interface ModelElementProcessor
{
    public function process(ProcessorContext $context, \ReflectionClass $mapperTypeElement, $sourceModel);

    public function getPriority(): int;
}
