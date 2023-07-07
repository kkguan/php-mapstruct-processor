<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Util;

use Kkguan\PHPMapstruct\Processor\Spi\AccessorNamingStrategyInterface;
use Kkguan\PHPMapstruct\Processor\Spi\MethodType;

class AccessorNamingUtils
{
    public function __construct(
        private AccessorNamingStrategyInterface $accessorNamingStrategy
    ) {
    }

    public function isGetterMethod(?\ReflectionMethod $executable): bool
    {
        if (empty($executable)) {
            return false;
        }

        if (Executables::isPublicNotStatic($executable)
            && empty($executable->getParameters())
            && ($this->accessorNamingStrategy->getMethodType($executable) == MethodType::GETTER)) {
            return true;
        }

        return false;
    }

    public function isSetterMethod(?\ReflectionMethod $executable): bool
    {
        if (empty($executable)) {
            return false;
        }

        if (Executables::isPublicNotStatic($executable)
            && count($executable->getParameters()) == 1
            && $this->accessorNamingStrategy->getMethodType($executable) == MethodType::SETTER) {
            return true;
        }

        return false;
    }

    public function getPropertyName(\ReflectionMethod $executable): string
    {
        return $this->accessorNamingStrategy->getPropertyName($executable);
    }

    public function isPresenceCheckMethod(\ReflectionMethod $executable): bool
    {
        if (empty($executable)) {
            return false;
        }

        if (Executables::isPublicNotStatic($executable)
            && empty($executable->getParameters())
            && ($this->accessorNamingStrategy->getMethodType($executable) == MethodType::PRESENCE_CHECKER)
            && $executable->hasReturnType()) {
            $returnType = $executable->getReturnType();

            if ($returnType instanceof \ReflectionNamedType
                && $returnType->isBuiltin()
                && $returnType->getName() == 'bool') {
                return true;
            }

            if ($returnType instanceof \ReflectionUnionType) {
                foreach ($returnType->getTypes() as $type) {
                    if ($type->getName() == 'bool' && $type->isBuiltin()) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
