<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Common;

use Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor\Accessor;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor\AccessorType;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor\MapValueAccessor;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor\PresenceCheckAccessor;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor\ReadAccessor;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor\ReadAccessorFactory;
use Kkguan\PHPMapstruct\Processor\Internal\Util\AccessorNamingUtils;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Filters;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Str;

class Type
{
    private ?array $alternativeTargetAccessors = null;

    /** @var PresenceCheckAccessor[] */
    private ?array $presenceCheckers = null;

    /** @var ReadAccessor[] */
    private ?array $readAccessors = null;

    /** @var null|Accessor[] */
    private ?array $setters = null;

    private Filters $filters;

    /** @var Type[] */
    private array $typeParameters = [];

    public function __construct(
        private TypeFactory $typeFactory,
        private AccessorNamingUtils $accessorNaming,
        private \ReflectionType $typeElement,
        private ?Type $componentType,
        private ?string $packageName,
        private string $name,
        private string $qualifiedName,
        private bool $isInterface,
        private bool $isEnumType,
        private bool $isCollectionType,
        private bool $isMapType,
        private bool $isLiteral,
        array $typeParameters = []
    ) {
        $this->typeParameters = $typeParameters;
        $this->filters = new Filters(accessorNaming: $accessorNaming);
    }

    /**
     * @param mixed $cmStrategy
     * @return Accessor[]
     */
    public function getPropertyWriteAccessors($cmStrategy): array
    {
        $result = [];
        $candidates = $this->getSetters();
        foreach ($candidates as $candidate) {
            $targetPropertyName = $this->getPropertyName($candidate);

//            $readAccessor = $this->getPropertyReadAccessors()[$targetPropertyName] ?? null;
//            $preferredType = $this->determinePreferredType($readAccessor);
//            $targetType = $this->determineTargetType($candidate);

            // TODO: 待实现策略

            $result[$targetPropertyName] = $candidate;

            // 这里是为了兼容方法名为驼峰，变量名为下划线的情况
            $accessorElement = $candidate->getElement();
            if ($accessorElement instanceof \ReflectionMethod) {
                $propertiesName = [];
                foreach ($accessorElement->getDeclaringClass()->getProperties() as $property) {
                    $propertiesName[] = $property->getName();
                }
                $newTargetPropertyName = Str::camelToUnderLine($targetPropertyName);

                if (
                    $accessorElement->getDeclaringClass()->hasProperty($targetPropertyName)
                    && in_array($newTargetPropertyName, $propertiesName)
                    && ! in_array($newTargetPropertyName, haystack: array_keys($result))
                ) {
                    $result[$newTargetPropertyName] = $candidate;
                }
            }
        }

        return $result;
    }

    /**
     * @return ReadAccessor[]
     */
    public function getPropertyReadAccessors(): array
    {
        if ($this->readAccessors === null) {
            $modifiableGetters = [];
            $getterList = $this->filters->getterMethodsIn($this->getAllMethods());

            foreach ($getterList as $getter) {
                $simpleName = $getter->getSimpleName();
                $propertyName = $this->getPropertyName($getter);

                $modifiableGetters[$propertyName] = $getter;
            }

            $fieldsList = $this->filters->fieldsIn($this->getAllFields(), function (\ReflectionProperty $property) {return ReadAccessorFactory::fromField($property); });
            foreach ($fieldsList as $field) {
                $propertyName = $this->getPropertyName($field);
                // If there was no getter or is method for booleans, then resort to the field.
                // If a field was already added do not add it again.
                if (! isset($modifiableGetters[$propertyName])) {
                    $modifiableGetters[$propertyName] = $field;
                }
            }

            $this->readAccessors = $modifiableGetters;
        }

        return $this->readAccessors;
    }

    public function withoutBounds(): Type
    {
        return $this;
    }

    public function getAlternativeTargetAccessors()
    {
    }

    public function getReadAccessor(string $propertyName): ?ReadAccessor
    {
        if ($this->hasStringMapSignature()) {
            $methods = $this->getAllMethods();
            $getMethod = null;
            foreach ($methods as $method) {
                if (strpos($method->getName(), 'get') !== false && count($method->getParameters()) === 1) {
                    $getMethod = $method;
                    break;
                }
            }
            return new MapValueAccessor($getMethod, $this->typeParameters[0]->getTypeMirror(), $propertyName);
        }

        /** @var array<string, ReadAccessor> $readAccessors */
        $readAccessors = $this->getPropertyReadAccessors();

        if (isset($readAccessors[$propertyName])) {
            return $readAccessors[$propertyName];
        }

        if (isset($readAccessors[Str::camel($propertyName)])) {
            return $readAccessors[Str::camel($propertyName)];
        }

        return null;
    }

    public function hasStringMapSignature()
    {
        if ($this->isMapType()) {
            $typeParameters = $this->getTypeParameters();
            if (count($typeParameters) == 2 && $typeParameters[0]->isString()) {
                return true;
            }
        }

        return false;
    }

    public function isString(): bool
    {
        return strtolower($this->getFullyQualifiedName()) === 'string';
    }

    public function isInteger(): bool
    {
        return strtolower($this->getFullyQualifiedName()) === 'int';
    }

    public function getPresenceChecker(string $propertyName): ?PresenceCheckAccessor
    {
        // TODO
//        if ($this->hasStringMapSignature()) {
//
//        }

        $presenceCheckers = $this->getPropertyPresenceCheckers();

        return $presenceCheckers[$propertyName] ?? null;
    }

    public function getFullyQualifiedName(): string
    {
        return $this->qualifiedName;
    }

    public function isAbstract(): bool
    {
        if ($this->typeElement instanceof \ReflectionNamedType) {
            if ($this->typeElement->isBuiltin()) {
                return false;
            }

            $reflectionClass = new \ReflectionClass($this->getFullyQualifiedName());
            return $reflectionClass->isAbstract();
        }
        // 未处理联合类型
        return false;
    }

    public function isPrimitive(): bool
    {
        if ($this->typeElement instanceof \ReflectionNamedType) {
            return $this->typeElement->isBuiltin();
        }
        return false;
    }

    public function describe()
    {
        // TODO
        return $this->getFullyQualifiedName();
    }

    public function getTypeElement(): \ReflectionType
    {
        return $this->typeElement;
    }

    public function getTypeMirror(): \ReflectionType
    {
        return $this->typeElement;
    }

    public function isMixed(): bool
    {
        return $this->getFullyQualifiedName() === 'mixed';
    }

    public function isAssignableTo(Type $other)
    {
        // TODO: 这里兼容了?xxx 这样的类型
        return trim((string) $this->getTypeMirror(), '?') === trim((string) $other->getTypeMirror(), '?');
    }

    public function isEnumType(): bool
    {
        return $this->isEnumType;
    }

    public function setIsEnumType(bool $isEnumType): Type
    {
        $this->isEnumType = $isEnumType;
        return $this;
    }

    public function getTypeFactory(): TypeFactory
    {
        return $this->typeFactory;
    }

    public function setTypeFactory(TypeFactory $typeFactory): Type
    {
        $this->typeFactory = $typeFactory;
        return $this;
    }

    public function getAccessorNaming(): AccessorNamingUtils
    {
        return $this->accessorNaming;
    }

    public function setAccessorNaming(AccessorNamingUtils $accessorNaming): Type
    {
        $this->accessorNaming = $accessorNaming;
        return $this;
    }

    public function getComponentType(): ?Type
    {
        return $this->componentType;
    }

    public function setComponentType(?Type $componentType): Type
    {
        $this->componentType = $componentType;
        return $this;
    }

    public function getPackageName(): ?string
    {
        return $this->packageName;
    }

    public function setPackageName(?string $packageName): Type
    {
        $this->packageName = $packageName;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Type
    {
        $this->name = $name;
        return $this;
    }

    public function getQualifiedName(): string
    {
        return $this->qualifiedName;
    }

    public function setQualifiedName(string $qualifiedName): Type
    {
        $this->qualifiedName = $qualifiedName;
        return $this;
    }

    public function isInterface(): bool
    {
        return $this->isInterface;
    }

    public function setIsInterface(bool $isInterface): Type
    {
        $this->isInterface = $isInterface;
        return $this;
    }

    public function isCollectionType(): bool
    {
        return $this->isCollectionType;
    }

    public function setIsCollectionType(bool $isCollectionType): Type
    {
        $this->isCollectionType = $isCollectionType;
        return $this;
    }

    public function isArrayType(): bool
    {
        return $this->componentType != null;
    }

    public function isPHPLangType(): bool
    {
        return $this->typeElement->isBuiltin();
    }

    public function isIterableOrStreamType()
    {
        // TODO: 不支持迭代器和流
        return false;
    }

    public function isMapType(): bool
    {
        return $this->isMapType;
    }

    public function isLiteral(): bool
    {
        return $this->isLiteral;
    }

    public function getTypeParameters(): array
    {
        return $this->typeParameters;
    }

    public function getDistanceTo(Type $otherType): int
    {
        // fixme:不一定正确
        if ($this === $otherType) {
            return 0;
        }

        if ($this instanceof $otherType) {
            return 1;
        }

        if ($otherType instanceof $this) {
            return 1;
        }

        return 2;
    }

    public function isVoid(): bool
    {
        return $this->typeElement->allowsNull();
    }

    public function isCollectionOrMapType()
    {
        return $this->isCollectionType() || $this->isMapType();
    }

    public function getNull(): string
    {
        // TODO: 这里支持不完全
        if (! $this->isPrimitive() || $this->isArrayType()) {
            return 'null';
        }

        if ($this->getName() === 'bool') {
            return 'false';
        }

        if ($this->getName() === 'string') {
            return "''";
        }

        return '0';
    }

    public function resolveParameterToType(Type $declared, Type $parameterized): ResolvedPair
    {
//        : ResolvedPair
        if ($this->isTypeVar() || $this->isArrayType()) {
//            $typeVarMatcher = new TypeVarMatcher($this->typeFactory, null, $this);
//
//
//            dump($declared, $parameterized);
//            dd(__METHOD__ . '::' . __LINE__);
//            return $typeVarMatcher->visit($declared, $parameterized);
            // TODO: 这里不支持泛型
        }

        return new ResolvedPair($this, $this);
    }

    public function isRawAssignableTo(Type $other): bool
    {
        if ($this->isMixed() || $other->isMixed()) {
            return true;
        }

        if ($other->getTypeMirror() === $this->getTypeMirror()) {
            return true;
        }

        return trim((string) $this->getTypeMirror(), '?') === trim((string) $other->getTypeMirror(), '?');
    }

    /**
     * @return Accessor[]
     */
    private function getSetters(): array
    {
        if ($this->setters === null) {
            $this->setters = $this->filters->setterMethodsIn($this->getAllMethods());
        }

        return $this->setters;
    }

    /**
     * @return \ReflectionMethod[]
     */
    private function getAllMethods(): array
    {
        $reflectionClass = new \ReflectionClass($this->qualifiedName);
        return $reflectionClass->getMethods();
    }

    /**
     * @return \ReflectionProperty[]
     */
    private function getAllFields(): array
    {
        $reflectionClass = new \ReflectionClass($this->qualifiedName);
        return $reflectionClass->getProperties();
    }

    private function getPropertyName(Accessor $candidate): string
    {
        $accessorElement = $candidate->getElement();
        if ($accessorElement instanceof \ReflectionMethod) {
//            return $this->accessorNaming->getPropertyName($accessorElement);
            return $this->getPropertyNameFromElement($accessorElement);
        }

        if ($accessorElement instanceof \ReflectionProperty) {
            return $accessorElement->getName();
        }

        return $candidate->getSimpleName();
    }

    private function getPropertyNameFromElement(\ReflectionMethod $element): string
    {
        return $this->accessorNaming->getPropertyName($element);
    }

//    public function accept(TypeVarMatcher $param, Type $parameterized)
//    {
//
//    }

    private function determinePreferredType(?Accessor $readAccessor): ?Type
    {
        if ($readAccessor !== null) {
            return $this->typeFactory->getReturnTypeByAccessor(new \ReflectionClass($this->qualifiedName), $readAccessor);
        }

        return null;
    }

    private function determineTargetType(Accessor $candidate): ?Type
    {
        $parameter = $this->typeFactory->getSingleParameter(new \ReflectionClass($this->qualifiedName), $candidate);
        if ($parameter !== null) {
            return $parameter->getType();
        }
        if ($candidate->getAccessorType() == AccessorType::GETTER || AccessorType::isFieldAssignment($candidate->getAccessorType())) {
            return $this->typeFactory->getReturnTypeByAccessor(new \ReflectionClass($this->qualifiedName), $candidate);
        }

        return null;
    }

    /**
     * @return PresenceCheckAccessor[]
     */
    private function getPropertyPresenceCheckers(): array
    {
        if ($this->presenceCheckers === null) {
            $checkerList = $this->filters->presenceCheckMethodsIn($this->getAllMethods());
            $modifiableCheckers = [];
            foreach ($checkerList as $checker) {
                $name = $this->getPropertyNameFromElement($checker);
                $modifiableCheckers[$name] = PresenceCheckAccessor::methodInvocation($checker);
            }

            $this->presenceCheckers = $modifiableCheckers;
        }

        return $this->presenceCheckers;
    }

    private function isTypeVar(): bool
    {
        // 这里判断的是否是泛形
        return (string) $this->typeElement === 'mixed';
//        return $this->typeElement instanceof \ReflectionType;
    }

    private function isWildCardBoundByTypeVar(): bool
    {
        return $this->typeElement instanceof \ReflectionType;
    }
}
