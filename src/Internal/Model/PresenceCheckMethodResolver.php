<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model;

class PresenceCheckMethodResolver
{
    public static function getPresenceCheck(Source\Method $method, Source\SelectionParameters $selectionParameters, MappingBuilderContext $ctx)
    {
        static::findMatchingPresenceCheckMethod(
            $method,
            $selectionParameters,
            $ctx
        );
    }

    private static function findMatchingPresenceCheckMethod(Source\Method $method, Source\SelectionParameters $selectionParameters, MappingBuilderContext $ctx)
    {
    }
}
