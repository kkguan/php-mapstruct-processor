<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Source;

use Kkguan\PHPMapstruct\Processor\Internal\Model\AbstractBaseBuilder;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor\Accessor;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor\AccessorType;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Accessor\ReadAccessor;

class MappingBuilderBase extends AbstractBaseBuilder
{
    protected ?string $targetPropertyName;

    protected ReadAccessor $targetReadAccessor;

    protected Accessor $targetWriteAccessor;

    protected Type $targetType;

    protected AccessorType $targetWriteAccessorType;

    private $positionHint;

    // TODO: 构建类型
//    protected BuilderType $builderType;

    public function sourceMethod(Method $method): static
    {
        return parent::method($method);
    }

    public function target(string $targetPropertyName, ReadAccessor $readAccessor, Accessor $targetWriteAccessor): static
    {
        $this->targetPropertyName = $targetPropertyName;
        $this->targetReadAccessor = $readAccessor;
        $this->targetWriteAccessor = $targetWriteAccessor;
        $this->targetType = $this->ctx->getTypeFactory()->getType($targetWriteAccessor->getAccessorType());
        $builder = $this->method->getOptions()->getBeanMapping()->getBuilder();
        // TODO
        // this.targetBuilderType = ctx.getTypeFactory().builderTypeFor( this.targetType, builder );
        $this->targetWriteAccessorType = $targetWriteAccessor->getAccessorType();
        return $this;
    }

    public function mirror($mirror): static
    {
        $this->positionHint = $mirror;
        return $this;
    }
}
