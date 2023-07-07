<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Processor;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\TypeFactory;
use Kkguan\PHPMapstruct\Processor\Internal\Option\Options;
use Kkguan\PHPMapstruct\Processor\Internal\Util\RoundContext;
use Symfony\Component\Filesystem\Filesystem;

class DefaultModelElementProcessorContext extends ProcessorContext
{
    private array $delegatingMessager = [];

    public function __construct(
        Options $options,
        RoundContext $roundContext,
    ) {
        $this->typeFactory = new TypeFactory(
            roundContext: $roundContext
        );
        $this->options = $options;
        $this->accessorNaming = $roundContext->getAnnotationProcessorContext()->getAccessorNaming();
        $this->roundContext = $roundContext;
    }

    public function isErroneous(): bool
    {
        return false;
    }

    public function getFiler()
    {
        // 先用 FileSystem
        return new Filesystem();
    }
}
