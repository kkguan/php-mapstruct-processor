<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Writer;

/**
 * 本文件属于KK集团版权所有，泄漏必究。
 */
interface Context
{
    /**
     * Retrieves the object with the given type from this context.
     * Params:
     * type – The type of the object to retrieve from this context.
     * Returns:
     * The object with the given type from this context.
     */
    public function get(mixed $type);
}
