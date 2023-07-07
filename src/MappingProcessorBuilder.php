<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor;

class MappingProcessorBuilder
{
    private array $annotationClasses = [];

    private array $annotationScanPaths = [];

    public function getAnnotationClasses(): array
    {
        return $this->annotationClasses;
    }

    public function setAnnotationClasses(array $annotationClasses): MappingProcessorBuilder
    {
        $this->annotationClasses = $annotationClasses;
        return $this;
    }

    public function getAnnotationScanPaths(): array
    {
        return $this->annotationScanPaths;
    }

    public function setAnnotationScanPaths(array $annotationScanPaths): MappingProcessorBuilder
    {
        $this->annotationScanPaths = $annotationScanPaths;
        return $this;
    }

    public function build(): void
    {
    }
}
