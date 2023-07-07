<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\SourceMethod;

abstract class MapperReference extends Field
{
    public function __construct(Type $type, string $variableName, bool $isUsed = false)
    {
        parent::__construct(type: $type, variableName: $variableName, used: $isUsed);
    }

    /**
     * @param MapperReference[] $mapperReferences
     */
    public function findMapperReference(array $mapperReferences, SourceMethod $sourceMethod): ?MapperReference
    {
        foreach ($mapperReferences as $mapperReference) {
            // TODO: 判断两个对象是否一样，目前实现是有问题的
            if ($mapperReference->getType() == $sourceMethod) {
                $mapperReference->setUsed($mapperReference->isUsed() || ! $sourceMethod->isStatic());
                $mapperReference->setTypeRequiresImport(true);
                return $mapperReference;
            }
        }
        return null;
    }
}
