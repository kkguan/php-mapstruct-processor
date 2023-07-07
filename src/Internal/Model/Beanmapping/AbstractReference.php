<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Beanmapping;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Parameter;

abstract class AbstractReference
{
    protected ?Parameter $parameter;

    /** @var PropertyEntry[] */
    protected array $propertyEntries;

    protected bool $isValid;

    public function __construct(
        ?Parameter $sourceParameter,
        array $sourcePropertyEntries,
        bool $isValid
    ) {
        $this->parameter = $sourceParameter;
        $this->propertyEntries = $sourcePropertyEntries;
        $this->isValid = $isValid;
    }

    public function __toString(): string
    {
        $result = '';
        if (! $this->isValid) {
            $result = 'invalid';
        } elseif (empty($this->propertyEntries)) {
            if ($this->parameter !== null) {
                $result = sprintf('parameter "%s %s"', $this->parameter->getType()?->describe(), $this->parameter->getName());
            }
        } elseif (count($this->propertyEntries) == 1) {
            $propertyEntry = $this->propertyEntries[0];
            $result = sprintf('property "%s %s"', $propertyEntry->getType()->describe(), $propertyEntry->getName());
        } else {
            $lastPropertyEntry = $this->getDeepestProperty();
            $result = sprintf('property "%s %s"', $lastPropertyEntry->getType()->describe(), implode('.', $this->getElementNames()));
        }

        return $result;
    }

    public function isNested(): bool
    {
        return count($this->propertyEntries) > 1;
    }

    public function getParameter(): ?Parameter
    {
        return $this->parameter;
    }

    /**
     * @return PropertyEntry[]
     */
    public function getPropertyEntries(): array
    {
        return $this->propertyEntries;
    }

    public function getElementNames(): array
    {
        $elementNames = [];
        if ($this->parameter != null) {
            $elementNames[] = $this->parameter->getName();
        }

        foreach ($this->propertyEntries as $propertyEntry) {
            $elementNames[] = $propertyEntry->getName();
        }

        return $elementNames;
    }

    public function getDeepestProperty(): ?PropertyEntry
    {
        if (empty($this->propertyEntries)) {
            return null;
        }

        return $this->propertyEntries[count($this->propertyEntries) - 1];
    }

    public function getDeepestPropertyName(): ?string
    {
        if (empty($this->propertyEntries)) {
            return null;
        }

        return $this->propertyEntries[count($this->propertyEntries) - 1]->getName();
    }
}
