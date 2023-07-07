<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Source;

use Kkguan\PHPMapstruct\Processor\Internal\Gem\BeanMappingGem;
use Kkguan\PHPMapstruct\Processor\Internal\Gem\BuilderGem;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\TypeFactory;

class BeanMappingOptions extends DelegatingOptions
{
    public function __construct(
        private ?SelectionParameters $selectionParameters,
        private ?BeanMappingGem $beanMapping,
        private DelegatingOptions $next
    ) {
        parent::__construct($next);
    }

    public static function getInstanceOn(
        ?BeanMappingGem $beanMapping,
        MapperOptions $mapperOptions,
        \ReflectionMethod $method,
        TypeFactory $typeFactory
    ): BeanMappingOptions {
        return new BeanMappingOptions(null, null, $mapperOptions);
    }

    public function getBuilder(): ?BuilderGem
    {
        if (empty($this->beanMapping)) {
            return null;
        }
        return $this->next->getBuilder();
    }

    public function getSelectionParameters(): ?SelectionParameters
    {
        return $this->selectionParameters;
    }
}
