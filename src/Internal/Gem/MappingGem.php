<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Gem;

use Kkguan\PHPMapstruct\Mapping;

class MappingGem
{
    public string $target = '';

    public string $source = '';

    public string $dateFormat = '';

    public string $numberFormat = '';

    public string $constant = '';

    public string $expression = '';

    public string $defaultExpression = '';

    public bool $ignore = false;

    private function __construct(
        string $target,
        string $source,
        string $dateFormat,
        string $numberFormat,
        string $constant,
        string $expression,
        string $defaultExpression,
        bool $ignore
    ) {
        $this->target = $target;
        $this->source = $source;
        $this->dateFormat = $dateFormat;
        $this->numberFormat = $numberFormat;
        $this->constant = $constant;
        $this->expression = $expression;
        $this->defaultExpression = $defaultExpression;
        $this->ignore = $ignore;
    }

    public static function instanceOn(\ReflectionAttribute $element): ?static
    {
        $elementObj = $element->newInstance();
        if ($elementObj::class != Mapping::class) {
            return null;
        }

        return new static(
            target: $elementObj->getTarget(),
            source: $elementObj->getSource(),
            dateFormat: $elementObj->getDateFormat(),
            numberFormat: $elementObj->getNumberFormat(),
            constant: $elementObj->getConstant(),
            expression: $elementObj->getExpression(),
            defaultExpression: $elementObj->getDefaultExpression(),
            ignore: $elementObj->isIgnore(),
        );
    }
}
