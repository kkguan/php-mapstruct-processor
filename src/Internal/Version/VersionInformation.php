<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Version;

interface VersionInformation
{
    public function getRuntimeVersion(): string;

    public function getRuntimeVendor(): string;

    public function getMapStructVersion(): string;

    public function getCompiler(): string;

    public function isSourceVersionAtLeast9(): bool;

    public function isEclipseJDTCompiler(): bool;

    public function isJavacCompiler(): bool;
}
