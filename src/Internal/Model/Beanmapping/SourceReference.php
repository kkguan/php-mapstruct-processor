<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Beanmapping;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Parameter;

class SourceReference extends AbstractReference
{
    /**
     * @param PropertyEntry[] $sourcePropertyEntries
     */
    public function __construct(
        ?Parameter $sourceParameter,
        array $sourcePropertyEntries,
        bool $isValid
    ) {
        parent::__construct($sourceParameter, $sourcePropertyEntries, $isValid);
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function getShallowestPropertyName()
    {
        if (empty($this->propertyEntries)) {
            return null;
        }

        return $this->propertyEntries[0]->getName();
    }
}
