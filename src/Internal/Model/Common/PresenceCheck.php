<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Common;

interface PresenceCheck
{
    /**
     * returns all types required as import by the presence check.
     * @return Type[] imported types
     */
    public function getImportTypes(): ?array;
}
