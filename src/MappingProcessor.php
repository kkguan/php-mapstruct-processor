<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor;

use Kkguan\PHPMapstruct\Mapper;
use Kkguan\PHPMapstruct\Processor\Internal\Option\Options;
use Kkguan\PHPMapstruct\Processor\Internal\Processor\DefaultModelElementProcessorContext;
use Kkguan\PHPMapstruct\Processor\Internal\Processor\MapperCreationProcessor;
use Kkguan\PHPMapstruct\Processor\Internal\Processor\MapperRenderingProcessor;
use Kkguan\PHPMapstruct\Processor\Internal\Processor\MethodRetrievalProcessor;
use Kkguan\PHPMapstruct\Processor\Internal\Processor\ModelElementProcessor;
use Kkguan\PHPMapstruct\Processor\Internal\Processor\ProcessorContext;
use Kkguan\PHPMapstruct\Processor\Internal\Util\AnnotationProcessorContext;
use Kkguan\PHPMapstruct\Processor\Internal\Util\RoundContext;

class MappingProcessor
{
    private Options $options;

    private AnnotationProcessorContext $annotationProcessorContext;

    public function init(Options $options): static
    {
        $this->annotationProcessorContext = new AnnotationProcessorContext();
        $this->options = $options;
        return $this;
    }

    public function process(MappingProcessorBuilder $builder)
    {
        $builder->build();
        $roundContext = new RoundContext($this->annotationProcessorContext);
        $mappers = $this->getMappers($builder);
        $this->processMapperElements($mappers, $roundContext);
    }

    /**
     * @return \ReflectionClass[]
     * @throws \ReflectionException
     */
    private function getMappers(MappingProcessorBuilder $builder): array
    {
        $mapperTypes = [];

        foreach ($builder->getAnnotationClasses() as $class) {
            $reflectionClass = new \ReflectionClass($class);
            $attributes = $reflectionClass->getAttributes();
            foreach ($attributes as $attribute) {
                $reflect = new \ReflectionClass($attribute->getName());
                if ($reflect->isSubclassOf(Mapper::class)) {
                    $mapperTypes[] = $reflectionClass;
                    break;
                }
            }
        }

        return $mapperTypes;
    }

    /**
     * @param \ReflectionClass[] $mapperElements
     */
    private function processMapperElements(array $mapperElements, RoundContext $roundContext): void
    {
        foreach ($mapperElements as $mapperElement) {
            $context = new DefaultModelElementProcessorContext(
                options: $this->options,
                roundContext: $roundContext,
            );
            $this->processMapperTypeElement(
                $context,
                $mapperElement,
            );
        }
    }

    private function processMapperTypeElement(ProcessorContext $context, \ReflectionClass $mapperTypeElement): void
    {
        $model = null;
        foreach ($this->getProcessors() as $processor) {
            $model = $processor->process($context, $mapperTypeElement, $model);
        }
    }

    /**
     * @return ModelElementProcessor[]
     */
    private function getProcessors(): array
    {
        return [
            new MethodRetrievalProcessor(),
            new MapperCreationProcessor(),
            new MapperRenderingProcessor(),
        ];
    }
}
