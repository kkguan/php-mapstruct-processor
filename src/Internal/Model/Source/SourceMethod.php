<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Source;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Parameter;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\TypeFactory;
use Kkguan\PHPMapstruct\Processor\Internal\Util\Executables;

class SourceMethod extends AbstractMethod
{
    private TypeFactory $typeFactory;

    private ?Type $declaringMapper;

    private \ReflectionMethod $executable;

    /** @var Parameter[] */
    private array $parameters;

    private ?Parameter $mappingTargetParameter;

    private ?Parameter $targetTypeParameter;

    private bool $isObjectFactory = false;

    private bool $isPresenceCheck = false;

    private ?Type $returnType;

    private Accessibility $accessibility;

    /** @var Type[] */
    private array $exceptionTypes;

    private ?MappingMethodOptions $mappingMethodOptions;

    /** @var SourceMethod[] */
    private array $prototypeMethods;

    private ?Type $mapperToImplement;

    /** @var Parameter[] */
    private array $sourceParameters;

    /** @var Parameter[] */
    private array $contextParameters;

    private ?ParameterProvidedMethods $contextProvidedMethods;

    /** @var Type[] */
    private array $typeParameters;

    private ?array $parameterNames = null;

    /** @var SourceMethod[] */
    private array $applicablePrototypeMethods;

    /** @var SourceMethod[] */
    private array $applicableReversePrototypeMethods;

    private bool $isValueMapping;

    private bool $isIterableMapping;

    private bool $isMapMapping;

    private bool $isStreamMapping;

    private bool $hasObjectFactoryAnnotation;

    private bool $verboseLogging;

    public function __construct(
        SourceMethodBuilder $builder,
        ?MappingMethodOptions $mappingMethodOptions
    ) {
        $this->declaringMapper = $builder->getDeclaringMapper();
        $this->executable = $builder->getExecutable();
        $this->parameters = $builder->getParameters();
        $this->returnType = $builder->getReturnType();
        $this->exceptionTypes = $builder->getExceptionTypes();
        $this->accessibility = Accessibility::fromModifiers($this->executable->getModifiers());

        $this->mappingMethodOptions = $mappingMethodOptions;

        $this->sourceParameters = Parameter::getSourceParameters($this->parameters);
        $this->contextParameters = Parameter::getContextParameters($this->parameters);
        $this->contextProvidedMethods = $builder->getContextProvidedMethods();
        $this->typeParameters = $builder->getTypeParameters();

        $this->mappingTargetParameter = Parameter::getMappingTargetParameter($this->parameters);
        $this->targetTypeParameter = Parameter::getTargetTypeParameter($this->parameters);
        // todo: ObjectFactory,Condition注解
        $this->hasObjectFactoryAnnotation = false;
        $this->isObjectFactory = false;
        $this->isPresenceCheck = false;
//        $this->isObjectFactory = determineIfIsObjectFactory();
//        $this->isPresenceCheck = determineIfIsPresenceCheck();

        $this->typeFactory = $builder->getTypeFactory();
        $this->prototypeMethods = $builder->getPrototypeMethods();

        $this->verboseLogging = $builder->isVerboseLogging();
    }

    /**
     * @param Parameter[] $parameters
     */
    public static function containsTargetTypeParameter(array $parameters): bool
    {
        foreach ($parameters as $parameter) {
            if ($parameter->isTargetType()) {
                return true;
            }
        }
        return false;
    }

    public function overridesMethod(): bool
    {
        return $this->declaringMapper == null && ($this->executable->getModifiers() & \ReflectionMethod::IS_ABSTRACT);
    }

    public function isLifecycleCallbackMethod(): bool
    {
        return Executables::isLifecycleCallbackMethod($this->getExecutable());
    }

    public function getOptions(): MappingMethodOptions
    {
        return $this->mappingMethodOptions;
    }

    public function getReturnType(): ?Type
    {
        return $this->returnType;
    }

    public function getResultType(): ?Type
    {
        return $this->mappingTargetParameter != null ? $this->mappingTargetParameter->getType() : $this->returnType;
    }

    public function getParameterNames(): array
    {
        if ($this->parameterNames === null) {
            $this->parameterNames = [];
            foreach ($this->parameters as $parameter) {
                $this->parameterNames[] = $parameter->getName();
            }
        }
        return $this->parameterNames;
    }

    /**
     * @return Parameter[]
     */
    public function getSourceParameters(): array
    {
        return $this->sourceParameters;
    }

    /**
     * @return Parameter[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getExecutable(): \ReflectionMethod
    {
        return $this->executable;
    }

    public function getMappingTargetParameter(): ?Parameter
    {
        return $this->mappingTargetParameter;
    }

    public function isUpdateMethod(): bool
    {
        return $this->getMappingTargetParameter() != null;
    }

    public function getName(): string
    {
        return $this->executable->getShortName();
    }

    public function getAccessibility(): Accessibility
    {
        return $this->accessibility;
    }

    public function isStatic(): bool
    {
        return $this->executable->isStatic();
    }

    public function isVoid(): bool
    {
        return $this->executable->getReturnType() === null;
    }

    public function isMappingTargetAssignableToReturnType(): bool
    {
        return true;
//        return $this->isUpdateMethod() && $this->getReturnType()->isAssignableTo($this->getReturnType());
    }

    public function isPublic(): bool
    {
        return $this->accessibility->isPublic();
    }

    public function isProtected(): bool
    {
        return $this->accessibility->isProtected();
    }

    public function isObjectFactory(): bool
    {
        return $this->isObjectFactory;
    }

    public function isPresenceCheck(): bool
    {
        return $this->isPresenceCheck;
    }

    public function getTypeFactory(): TypeFactory
    {
        return $this->typeFactory;
    }

    public function getDeclaringMapper(): ?Type
    {
        return $this->declaringMapper;
    }

    public function getTargetTypeParameter(): ?Parameter
    {
        return $this->targetTypeParameter;
    }

    public function getExceptionTypes(): array
    {
        return $this->exceptionTypes;
    }

    public function getMappingMethodOptions(): MappingMethodOptions
    {
        return $this->mappingMethodOptions;
    }

    public function getPrototypeMethods(): array
    {
        return $this->prototypeMethods;
    }

    public function getMapperToImplement(): ?Type
    {
        return $this->mapperToImplement;
    }

    public function getContextParameters(): array
    {
        return $this->contextParameters;
    }

    public function getContextProvidedMethods(): ?ParameterProvidedMethods
    {
        return $this->contextProvidedMethods;
    }

    public function getTypeParameters(): array
    {
        return $this->typeParameters;
    }

    public function getApplicablePrototypeMethods(): array
    {
        return $this->applicablePrototypeMethods;
    }

    public function getApplicableReversePrototypeMethods(): array
    {
        return $this->applicableReversePrototypeMethods;
    }

    public function isValueMapping(): bool
    {
        return $this->isValueMapping;
    }

    public function isIterableMapping(): bool
    {
        return $this->isIterableMapping;
    }

    public function isMapMapping(): bool
    {
        return $this->isMapMapping;
    }

    public function isStreamMapping(): bool
    {
        return $this->isStreamMapping;
    }

    public function isHasObjectFactoryAnnotation(): bool
    {
        return $this->hasObjectFactoryAnnotation;
    }

    public function isVerboseLogging(): bool
    {
        return $this->verboseLogging;
    }

    public function isAbstract(): bool
    {
        return $this->executable->getModifiers() === \ReflectionMethod::IS_ABSTRACT;
    }

    public function isDefault(): bool
    {
        // TODO: 未实现
        return false;
    }
}
