<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model;

use Kkguan\PHPMapstruct\Processor\Exception\IllegalArgumentException;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Parameter;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Method;

class NestedPropertyMappingMethod extends MappingMethod
{
    /**
     * @var SafePropertyEntry[]
     */
    private array $safePropertyEntries = [];

    /**
     * @param SafePropertyEntry[] $sourcePropertyEntries
     */
    public function __construct(Method $method, array $sourcePropertyEntries)
    {
        parent::__construct($method, $method->getParameters(), [], [], []);
        $this->safePropertyEntries = $sourcePropertyEntries;
    }

    public function getPropertyEntries(): array
    {
        return $this->safePropertyEntries;
    }

    public function getSourceParameter(): Parameter
    {
        foreach ($this->getParameters() as $parameter) {
            if (! $parameter->isMappingTarget() && ! $parameter->isMappingContext()) {
                return $parameter;
            }
        }

        throw new IllegalArgumentException('Method ' . $this->getName() . ' has no source parameter');
    }

    public function getTemplate()
    {
        return 'nestedPropertyMappingMethod.twig';
    }
}
