<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Spi;

use Kkguan\PHPMapstruct\Processor\Internal\Util\IntrospectorUtils;

class DefaultAccessorNamingStrategy implements AccessorNamingStrategyInterface
{
    public function getPropertyName(\ReflectionMethod $getterOrSetterMethod): string
    {
        $methodName = $getterOrSetterMethod->getShortName();
        $length = str_starts_with($methodName, 'is') ? 2 : 3;

        return IntrospectorUtils::decapitalize(substr($methodName, $length));
    }

    public function getMethodType(\ReflectionMethod $method): string
    {
        if ($this->isGetterMethod($method)) {
            return MethodType::GETTER;
        }
        if ($this->isSetterMethod($method)) {
            return MethodType::SETTER;
        }
        if ($this->isAdderMethod($method)) {
            return MethodType::ADDER;
        }
        if ($this->isPresenceCheckMethod($method)) {
            return MethodType::PRESENCE_CHECKER;
        }

        return MethodType::OTHER;
    }

    private function isGetterMethod(\ReflectionMethod $method): bool
    {
        if (! empty($method->getParameters())) {
            return false;
        }

//        $isNonBooleanGetterName = str_starts_with($method->getShortName(), 'get') && strlen($method->getShortName()) > 3 && $method->hasReturnType();
        $isNonBooleanGetterName = str_starts_with($method->getShortName(), 'get') && strlen($method->getShortName()) > 3;
        $isBooleanGetterName = str_starts_with($method->getShortName(), 'is') && strlen($method->getShortName()) > 2;
        $returnTypeIsBoolean = $method->getReturnType() instanceof \ReflectionNamedType && $method->getReturnType()->isBuiltin() && $method->getReturnType()->getName() == 'bool';

        return $isNonBooleanGetterName || ($isBooleanGetterName && $returnTypeIsBoolean);
    }

    private function isSetterMethod(\ReflectionMethod $method): bool
    {
        return str_starts_with($method->getShortName(), 'set') && strlen($method->getShortName()) > 3;
    }

    private function isAdderMethod(\ReflectionMethod $method): bool
    {
        return str_starts_with($method->getShortName(), 'add') && strlen($method->getShortName()) > 3;
    }

    private function isPresenceCheckMethod(\ReflectionMethod $method): bool
    {
        return str_starts_with($method->getShortName(), 'has') && strlen($method->getShortName()) > 3;
    }
}
