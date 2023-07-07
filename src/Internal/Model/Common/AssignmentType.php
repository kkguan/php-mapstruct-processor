<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Common;

class AssignmentType
{
    public const DIRECT = 'DIRECT';

    public const TYPE_CONVERTED = 'TYPE_CONVERTED';

    public const MAPPED = 'MAPPED';

    public const MAPPED_TWICE = 'MAPPED_TWICE';

    public const MAPPED_TYPE_CONVERTED = 'MAPPED_TYPE_CONVERTED';

    public const TYPE_CONVERTED_MAPPED = 'TYPE_CONVERTED_MAPPED';

    private bool $direct;

    private bool $converted;

    private string $type;

    public function __construct(string $type)
    {
        $this->type = $type;
        $this->initType($type);
    }

    public function isDirect(): bool
    {
        return $this->direct;
    }

    public function setDirect(bool $direct): AssignmentType
    {
        $this->direct = $direct;
        return $this;
    }

    public function isConverted(): bool
    {
        return $this->converted;
    }

    public function setConverted(bool $converted): AssignmentType
    {
        $this->converted = $converted;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): AssignmentType
    {
        $this->type = $type;
        $this->initType($type);
        return $this;
    }

    private function initType($type): void
    {
        switch ($type) {
            case AssignmentType::DIRECT:
                $this->setDirect(true);
                $this->setConverted(false);
                break;
            case AssignmentType::TYPE_CONVERTED_MAPPED:
            case AssignmentType::TYPE_CONVERTED:
                $this->setDirect(false);
                $this->setConverted(true);
                break;
            case AssignmentType::MAPPED_TWICE:
            case AssignmentType::MAPPED:
                $this->setDirect(false);
                $this->setConverted(false);
                break;
            case AssignmentType::MAPPED_TYPE_CONVERTED:
                $this->setDirect(true);
                $this->setConverted(true);
                break;
            default:
                throw new \RuntimeException(sprintf('unsupported %s type', $type));
        }
    }
}
