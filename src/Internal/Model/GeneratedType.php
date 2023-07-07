<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\ModelElement;

abstract class GeneratedType extends ModelElement
{
    private string $namespace = '';

    public function getImportTypes(): array
    {
        // TODO: Implement getImportTypes() method.
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function setNamespace(string $namespace): GeneratedType
    {
        $this->namespace = $namespace;
        return $this;
    }

    public function hasNamespace(): bool
    {
        return ! empty($this->namespace);
    }
}
