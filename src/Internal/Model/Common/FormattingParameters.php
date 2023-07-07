<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Common;

class FormattingParameters
{
    public function __construct(
        private ?string $date,
        private ?string $number,
        private $mirror,
        private $dateAnnotationValue,
        private $element
    ) {
    }

    public static function empty()
    {
        return new FormattingParameters(null, null, null, null, null);
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }
}
