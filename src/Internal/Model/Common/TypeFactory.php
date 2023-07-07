<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Common;

use Kkguan\PHPMapstruct\Processor\Internal\Gem\BuilderGem;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor\Accessor;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor\AccessorType;
use Kkguan\PHPMapstruct\Processor\Internal\Util\RoundContext;

class TypeFactory
{
    public function __construct(
        private RoundContext $roundContext
    ) {
    }

    /**
     * @return Parameter[]
     */
    public function getParameters(\ReflectionMethod $method): array
    {
        $parameters = [];
        foreach ($method->getParameters() as $parameter) {
            $type = $this->getType($parameter->getType());
            $parameter = Parameter::forElementAndType($parameter, $type);
            $parameters[] = $parameter;
        }

        return $parameters;
    }

    public function getTypeByMethod(\ReflectionMethod $method)
    {
    }

    public function getTypeByClass(\ReflectionClass $class)
    {
    }

    public function getType(?\ReflectionType $typeElement): Type
    {
        if (empty($typeElement)) {
            $typeElement = new ReflectionType('mixed', true);
        }

        if (! $this->canBeProcessed($typeElement)
            || ! ($typeElement instanceof \ReflectionNamedType)) {
            throw new \TypeHierarchyErroneousException();
        }

        // TODO: 待实现
        $isEnumType = enum_exists($typeElement->getName());
        $isInterface = false;
        $name = $qualifiedName = $typeElement->getName();
        $packageName = null;
        $isCollectionType = false;
        $element = null;
        $componentType = null;
        $isMapType = false;
        $isLiteral = false;
        if (! $typeElement->isBuiltin()) {
            $isInterface = interface_exists($typeElement->getName());
            $element = new \ReflectionClass($typeElement->getName());
            $name = $element->getShortName();
            $packageName = $element->getNamespaceName();
        }

        return new Type(
            typeFactory: $this,
            accessorNaming: $this->roundContext->getAnnotationProcessorContext()->getAccessorNaming(),
            typeElement: $typeElement,
            componentType: $componentType,
            packageName: $packageName,
            name: $name,
            qualifiedName: $qualifiedName,
            isInterface: $isInterface,
            isEnumType: $isEnumType,
            isCollectionType: $isCollectionType,
            isMapType: $isMapType,
            isLiteral: $isLiteral,
            typeParameters: []
        );
    }

    public function getReturnType(\ReflectionMethod $method): ?Type
    {
        return $method->getReturnType() != null ? $this->getType($method->getReturnType()) : null;
    }

    public function getReturnTypeByAccessor(\ReflectionClass $includingType, Accessor $accessor): ?Type
    {
        $accessorType = $this->getMethodType($includingType, $accessor->getSimpleName());
        if ($accessorType !== null) {
            return $this->getType($accessorType->getReturnType());
        }
        return null;
    }

    public function getSingleParameter(\ReflectionClass $includingType, Accessor $method): ?Parameter
    {
        if (AccessorType::isFieldAssignment($method->getAccessorType()) || $this->getMethodType($includingType, $method->getSimpleName()) === null) {
            return null;
        }
        /** @var \ReflectionMethod $executable */
        $executable = $method->getElement();
        $parameters = $executable->getParameters();
        if (count($parameters) != 1) {
            return null;
        }

        $parameter = $parameters[0];
        $type = $parameter->hasType() ? $this->getType($parameter->getType()) : null;
        return Parameter::forElementAndType($parameter, $type);
    }

    public function builderTypeFor(?Type $type, BuilderGem $builder)
    {
        if ($type !== null) {
            $builderInfo = $this->findBuilder($type->getTypeMirror(), $builder, true);
        }

        return null;
    }

    public function getMethodType(\ReflectionClass $includingType, $methodName): ?\ReflectionMethod
    {
        if ($includingType->hasMethod($methodName)) {
            return $includingType->getMethod($methodName);
        }

        return null;
    }

    public function getMethodTypeByDeclaredType(\ReflectionClass $includingType, \ReflectionMethod $methodName)
    {
        if ($includingType->hasMethod($methodName->getName())) {
            return $includingType->getMethod($methodName->getName());
        }

        return null;
    }

    public function getExceptionTypes(\ReflectionMethod $reflectionMethod)
    {
        // TODO: 待实现
        return [];
    }

    public function getWrappedType(Type $type): Type
    {
        $result = $type;
        if ($type->isPrimitive()) {
            $typeMirror = $type->getTypeMirror();
            $result = $this->getType($typeMirror);
        }
        return $result;
    }

    private function canBeProcessed(\ReflectionType $typeElement): bool
    {
        if ($typeElement instanceof \ReflectionUnionType) {
            return false;
        }

        return true;
    }

    private function findBuilder(\ReflectionType $type, ?BuilderGem $builderGem, bool $report)
    {
        // TODO
        if ($builderGem != null && $builderGem->disableBuilder()) {
            return null;
        }
        try {
            return $this->roundContext->getAnnotationProcessorContext()->getBuilderProvider()->findBuilderInfo($type);
        } catch (\Throwable $throwable) {
            if ($report) {
                // TODO: 日志信息
            }
        }
        return null;
    }
}
