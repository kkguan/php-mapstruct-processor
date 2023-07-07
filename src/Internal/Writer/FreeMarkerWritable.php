<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Writer;

/**
 * 本文件属于KK集团版权所有，泄漏必究。
 */
abstract class FreeMarkerWritable implements Writable
{
    public function write(Context $context, mixed $writer)
    {
        // TODO: 文件写入
    }

    public function getTemplate(): string
    {
        $class = substr(static::class, strrpos(static::class, '\\') + 1);
        return sprintf('%s.twig', $class);
    }

    protected function getTemplateName()
    {
        return $this->getTemplateNameForClass(static::class);
    }

    protected function getTemplateNameForClass(string $class)
    {
        // 去除命名空间
        return substr($class, strrpos($class, '\\') + 1);
    }
}
