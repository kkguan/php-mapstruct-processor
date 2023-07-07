<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\ModelElement;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;

class Field extends ModelElement
{
    private Type $type;

    private string $variableName;

    private bool $used;

    private bool $typeRequiresImport;

    public function __construct(Type $type, string $variableName, bool $used = false)
    {
        $this->type = $type;
        $this->variableName = $variableName;
        $this->used = $used;
        $this->typeRequiresImport = $used;
    }

    public function getImportTypes(): array
    {
        // TODO: Implement getImportTypes() method.
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function setType(Type $type): Field
    {
        $this->type = $type;
        return $this;
    }

    public function getVariableName(): string
    {
        return $this->variableName;
    }

    public function setVariableName(string $variableName): Field
    {
        $this->variableName = $variableName;
        return $this;
    }

    public function isUsed(): bool
    {
        return $this->used;
    }

    public function setUsed(bool $used): Field
    {
        $this->used = $used;
        return $this;
    }

    public function isTypeRequiresImport(): bool
    {
        return $this->typeRequiresImport;
    }

    public function setTypeRequiresImport(bool $typeRequiresImport): Field
    {
        $this->typeRequiresImport = $typeRequiresImport;
        return $this;
    }
}
