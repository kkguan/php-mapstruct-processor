<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Common;

use Kkguan\PHPMapstruct\Processor\Internal\Writer\FreeMarkerWritable;

abstract class ModelElement extends FreeMarkerWritable
{
    /**
     * Returns a set containing those Types referenced by this model element for which an import statement needs to be declared.
     * Returns: A set with type referenced by this model element. Must not be null.
     */
    abstract public function getImportTypes(): ?array;
}
