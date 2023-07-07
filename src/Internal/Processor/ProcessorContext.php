<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Processor;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\TypeFactory;
use Kkguan\PHPMapstruct\Processor\Internal\Option\Options;
use Kkguan\PHPMapstruct\Processor\Internal\Util\AccessorNamingUtils;
use Kkguan\PHPMapstruct\Processor\Internal\Util\RoundContext;

abstract class ProcessorContext
{
    protected TypeFactory $typeFactory;

    protected Options $options;

    protected RoundContext $roundContext;

    protected AccessorNamingUtils $accessorNaming;

    public function getTypeFactory(): TypeFactory
    {
        return $this->typeFactory;
    }

    public function getOptions(): Options
    {
        return $this->options;
    }

    public function getRoundContext(): RoundContext
    {
        return $this->roundContext;
    }

    public function getAccessorNaming(): AccessorNamingUtils
    {
        return $this->accessorNaming;
    }

    abstract public function isErroneous(): bool;

    abstract public function getFiler();
}
