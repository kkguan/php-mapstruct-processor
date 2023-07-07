<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Util;

class RoundContext
{
//    private AnnotationProcessorContext $annotationProcessorContext;

    private array $clearedTypes;

    public function __construct(
        private AnnotationProcessorContext $annotationProcessorContext
    ) {
        $this->clearedTypes = [];
    }

    public function getAnnotationProcessorContext(): AnnotationProcessorContext
    {
        return $this->annotationProcessorContext;
    }
}
