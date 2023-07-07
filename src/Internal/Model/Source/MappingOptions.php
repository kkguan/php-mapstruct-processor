<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Source;

use Kkguan\PHPMapstruct\Mapping;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\FormattingParameters;

class MappingOptions extends DelegatingOptions
{
    public function __construct(
        public string $target,
        public string $source,
        private \ReflectionAttribute $element,
        private ?InheritContext $inheritContext,
        private ?FormattingParameters $formattingParameters,
        ?DelegatingOptions $next,
        private bool $isIgnored = false,
    ) {
        parent::__construct($next);
    }

    public static function addInstance(\ReflectionAttribute $element, DelegatingOptions $option): ?static
    {
        $elementObj = $element->newInstance();
        if ($elementObj::class != Mapping::class) {
            return null;
        }

        $elementArguments = $element->getArguments();
        $dateFormat = $elementArguments['dateFormat'] ?? null;
        $numberFormat = $elementArguments['numberFormat'] ?? null;
        $defaultValue = $elementArguments['defaultValue'] ?? null;

        $formattingParam = new FormattingParameters(
            date: $dateFormat,
            number: $numberFormat,
            mirror: null,
            dateAnnotationValue: null,
            element: $element
        );

        // TODO: 忽略
        return new static(
            target: $elementObj->getTarget(),
            source: $elementObj->getSource(),
            element: $element,
            inheritContext: null,
            formattingParameters: $formattingParam,
            next: $option,
            isIgnored: $elementObj->isIgnore()
        );
    }

    public function getSourceName()
    {
        return $this->source;
    }

    public function getTargetName()
    {
        return $this->target;
    }

    public function getInheritContext()
    {
        return $this->inheritContext;
    }

    public function getFormattingParameters(): FormattingParameters
    {
        return $this->formattingParameters;
    }

    public function isIgnored(): bool
    {
        return $this->isIgnored;
    }

    public function setIsIgnored(bool $isIgnored): MappingOptions
    {
        $this->isIgnored = $isIgnored;
        return $this;
    }
}
