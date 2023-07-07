<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Writer;

/**
 * 本文件属于KK集团版权所有，泄漏必究。
 */
interface Writable
{
    /**
     * TODO: 这里的 writer 还未定义.
     *
     * @return mixed
     */
    public function write(Context $context, mixed $writer);
}
