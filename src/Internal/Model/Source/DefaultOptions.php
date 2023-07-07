<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Source;

use Kkguan\PHPMapstruct\Processor\Internal\Gem\MapperGem;
use Kkguan\PHPMapstruct\Processor\Internal\Option\Options;

class DefaultOptions extends DelegatingOptions
{
    private MapperGem $mapper;

    private Options $options;

    public function __construct(MapperGem $mapper, Options $options)
    {
        parent::__construct(null);
        $this->mapper = $mapper;
        $this->options = $options;
    }
}
