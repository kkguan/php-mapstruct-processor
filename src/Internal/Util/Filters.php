<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Util;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\ReflectionType;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor\Accessor;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor\AccessorType;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor\ExecutableElementAccessor;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor\ReadAccessor;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor\ReadAccessorFactory;

class Filters
{
    public function __construct(
        private AccessorNamingUtils $accessorNaming
    ) {
    }

    /**
     * @param \ReflectionMethod[] $elements
     * @return Accessor[]
     */
    public function setterMethodsIn(array $elements): array
    {
        $accessor = [];
        foreach ($elements as $element) {
            if (! $this->accessorNaming->isSetterMethod($element)) {
                continue;
            }

            $firstParameter = $this->getFirstParameter($element);
            $accessor[] = new ExecutableElementAccessor($element, $firstParameter, AccessorType::SETTER);
        }

        return $accessor;
    }

    /**
     * @param \ReflectionMethod[] $elements
     * @return \ReflectionMethod[]
     */
    public function presenceCheckMethodsIn(array $elements): array
    {
        $methods = [];
        foreach ($elements as $element) {
            if (! $this->accessorNaming->isPresenceCheckMethod($element)) {
                continue;
            }

            $methods[] = $element;
        }

        return $methods;
    }

    /**
     * @param \ReflectionMethod[] $elements
     * @return ReadAccessor[]
     */
    public function getterMethodsIn(array $elements): array
    {
        $accessor = [];

        foreach ($elements as $element) {
            if (! $this->accessorNaming->isGetterMethod($element)) {
                continue;
            }

            $returnType = $this->getReturnType($element);
            $accessor[] = ReadAccessorFactory::fromGetter($element, $returnType);
        }

        return $accessor;
    }

    /**
     * @param \ReflectionProperty[] $elements
     * @return ReadAccessor[]
     */
    public function fieldsIn(array $elements, callable $creater): array
    {
        $accessors = [];
        foreach ($elements as $element) {
            if (! Fields::isFieldAccessor($element)) {
                continue;
            }

            $accessors[] = $creater($element);
        }

        return $accessors;
    }

    private function getFirstParameter(\ReflectionMethod $element): \ReflectionParameter
    {
        return $element->getParameters()[0];
    }

    private function getReturnType(\ReflectionMethod $element): ?\ReflectionType
    {
        if (empty($element->getReturnType())) {
            return new ReflectionType('mixed', true);
        }

        return $element->getReturnType();
    }
}
