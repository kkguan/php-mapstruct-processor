<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Conversion;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\ReflectionType;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\TypeFactory;

class Conversions
{
    // 转换关系
    private array $conversions = [];

    private Type $enumType;

    public function __construct(private TypeFactory $typeFactory)
    {
        // 转换关系注册
        $this->registerByString('?int', 'float', new IntegerToFloatConversion());
        $this->registerByString('?int', '?float', new IntegerToFloatConversion());
        $this->registerByString('?int', 'bool', new IntegerToBoolConversion());
        $this->registerByString('?int', '?bool', new IntegerToBoolConversion());

        $this->registerByString('string', 'int', new StringToIntegerConversion());
        $this->registerByString('?string', 'int', new StringToIntegerConversion());
        $this->registerByString('string', '?int', new StringToIntegerConversion());
        $this->registerByString('?string', '?int', new StringToIntegerConversion());
        $this->registerByString('?string', 'float', new StringToFloatConversion());
        $this->registerByString('?string', '?float', new StringToFloatConversion());
        $this->registerByString('?string', 'bool', new StringToBoolConversion());
        $this->registerByString('?string', '?bool', new StringToBoolConversion());

        $this->registerByString('mixed', '?int', new MixedToIntegerConversion());
        $this->registerByString('mixed', 'int', new MixedToIntegerConversion());
        $this->registerByString('mixed', 'string', new MixedToStringConversion());
        $this->registerByString('mixed', '?string', new MixedToStringConversion());
        $this->registerByString('mixed', 'float', new MixedToFloatConversion());
        $this->registerByString('mixed', '?float', new MixedToFloatConversion());
        $this->registerByString('mixed', 'bool', new MixedToBoolConversion());
        $this->registerByString('mixed', '?bool', new MixedToBoolConversion());
        $this->registerByString('mixed', 'array', new MixedToArrayConversion());

        $this->registerByString('array', 'mixed', new ArrayToMixedConversion());
        $this->registerByString('array', 'int', new ArrayToIntConversion());
        $this->registerByString('array', '?int', new ArrayToIntConversion());
        $this->registerByString('array', 'string', new ArrayToStringConversion());
        $this->registerByString('array', '?string', new ArrayToStringConversion());
        $this->registerByString('array', 'float', new ArrayToFloatConversion());
        $this->registerByString('array', '?float', new ArrayToFloatConversion());
        $this->registerByString('array', 'bool', new ArrayToBoolConversion());
        $this->registerByString('array', '?bool', new ArrayToBoolConversion());

        $this->registerByString('float', 'int', new FloatToIntConversion());
        $this->registerByString('float', '?int', new FloatToIntConversion());
        $this->registerByString('float', 'string', new FloatToStringConversion());
        $this->registerByString('float', '?string', new FloatToStringConversion());
        $this->registerByString('float', 'bool', new FloatToBoolConversion());
        $this->registerByString('float', '?bool', new FloatToBoolConversion());
        $this->registerByString('float', 'array', new FloatToArrayConversion());
        $this->registerByString('float', '?array', new FloatToArrayConversion());
        $this->registerByString('float', 'mixed', new FloatToMixedConversion());
        $this->registerByString('float', '?mixed', new FloatToMixedConversion());

        if (have_enum()) {
            $this->enumType = $this->typeFactory->getType(new ReflectionType(\BackedEnum::class));
            $this->registerByString(\BackedEnum::class, 'string', new EnumToStringConversion());
            $this->registerByString(\BackedEnum::class, '?string', new EnumToStringConversion());
            $this->registerByString(\BackedEnum::class, 'int', new EnumToIntegerConversion());
            $this->registerByString(\BackedEnum::class, '?int', new EnumToIntegerConversion());
        }
    }

    public function getConversion(Type $sourceType, Type $targetType): ?ConversionProvider
    {
        if ($sourceType->isEnumType() && ($targetType->isString() || $targetType->isInteger())) {
            $sourceType = $this->enumType;
        } elseif ($targetType->isEnumType() && ($sourceType->isString() || $sourceType->isInteger())) {
            $targetType = $this->enumType;
        }
        return $this->conversions[$sourceType->getFullyQualifiedName()][$targetType->getFullyQualifiedName()] ?? null;
    }

    private function registerByType(Type $sourceType, Type $targetType, ConversionProvider $conversion)
    {
        $sourceType = $this->typeFactory->getType($sourceType)->getName();
        $targetType = $this->typeFactory->getType($targetType)->getName();
        $this->conversions[$sourceType][$targetType] = $conversion;
        $this->conversions[$targetType][$sourceType] = $this->inverse($conversion);
    }

    private function registerByString(string $sourceType, string $targetType, ConversionProvider $conversion)
    {
        $this->conversions[$sourceType][$targetType] = $conversion;
        $this->conversions[$targetType][$sourceType] = $this->inverse($conversion);
    }

    private function inverse(ConversionProvider $conversion): ReverseConversion
    {
        return new ReverseConversion($conversion);
    }
}
