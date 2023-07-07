<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Common;

use Psr\Log\LoggerInterface;

class DefaultConversionContext implements ConversionContext
{
    private ?string $dateFormat;

    private ?string $numberFormat;

    public function __construct(
        private TypeFactory $typeFactory,
        private LoggerInterface $messager,
        private Type $sourceType,
        private Type $targetType,
        private FormattingParameters $formattingParameters
    ) {
        $this->dateFormat = $this->formattingParameters->getDate();
        $this->numberFormat = $this->formattingParameters->getNumber();
    }

    public function getDateFormat(): ?string
    {
        return $this->dateFormat;
    }

    public function getNumberFormat(): ?string
    {
        return $this->numberFormat;
    }

    public function getTypeFactory(): TypeFactory
    {
        return $this->typeFactory;
    }

    public function getMessager(): LoggerInterface
    {
        return $this->messager;
    }

    public function getSourceType(): Type
    {
        return $this->sourceType;
    }

    public function getTargetType(): Type
    {
        return $this->targetType;
    }

    public function getFormattingParameters(): FormattingParameters
    {
        return $this->formattingParameters;
    }
}
