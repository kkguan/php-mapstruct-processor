<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Source;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Parameter;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;

interface Method
{
    public function getOptions(): MappingMethodOptions;

    public function isLifecycleCallbackMethod(): bool;

    public function isUpdateMethod(): bool;

    public function getName(): string;

    public function getAccessibility(): Accessibility;

    public function isStatic(): bool;

    /**
     * @return Parameter[]
     */
    public function getParameters(): array;

    public function overridesMethod(): bool;

    public function getReturnType(): ?Type;

    public function isObjectFactory(): bool;

    public function isPresenceCheck(): bool;

    public function isDefault(): bool;

    public function getMappingSourceType(): ?Type;

    /**
     * @return Parameter[]
     */
    public function getSourceParameters(): array;
}
