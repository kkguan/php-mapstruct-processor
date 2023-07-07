<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Assignment;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\AssignmentType;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\ModelElement;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Parameter;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\ParameterBinding;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\PresenceCheck;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\the;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Method;

class MethodReference extends ModelElement implements Assignment
{
    private string $name;

    /**
     * @var Parameter[]
     */
    private array $sourceParameters;

    private Type $returnType;

    private MapperReference $declaringMapper;

    /**
     * @var Type[]
     */
    private array $importTypes;

    /**
     * @var Type[]
     */
    private array $thrownTypes;

    private bool $isUpdateMethod;

    /**
     * In case this reference targets a built-in method, allows to pass specific context information to the invoked
     * method. Currently this is only used to pass in the configured date format string when invoking a built-in method
     * which requires that.
     */
    private string $contextParam;

    /**
     * A reference to another mapping method or typeConversion in case this is a two-step mapping, e.g. from
     * {@code JAXBElement<Bar>} to {@code Foo} to for which a nested method call will be generated:
     * {@code setFoo(barToFoo( jaxbElemToValue( bar) ) )}. If there's no nested typeConversion or other mapping method,
     * this will be a direct assignment.
     */
    private Assignment $assignment;

    private Type $definingType;

    /**
     * @var ParameterBinding[]
     */
    private array $parameterBindings;

    private Parameter $providingParameter;

    /**
     * @var MethodReference[]
     */
    private array $methodsToChain;

    private bool $isStatic;

    private bool $isConstructor;

    private bool $isMethodChaining;

    private ?string $sourceLocalVarName = null;

    /**
     * @param ParameterBinding[] $parameterBindings
     */
    public function __construct(Method $method, MapperReference $declaringMapper, array $parameterBindings, ?Parameter $providingParameter)
    {
        // TODO: 未实现完成
        $this->name = $method->getName();
        $this->sourceParameters = Parameter::getSourceParameters($method->getParameters());
        $this->returnType = $method->getReturnType();
        $this->declaringMapper = $declaringMapper;
//        $this->importTypes = $importTypes;
//        $this->thrownTypes = $thrownTypes;
//        $this->isUpdateMethod = $isUpdateMethod;
//        $this->contextParam = $contextParam;
//        $this->assignment = $assignment;
//        $this->definingType = $definingType;
//        $this->parameterBindings = $parameterBindings;
//        $this->providingParameter = $providingParameter;
//        $this->methodsToChain = $methodsToChain;
//        $this->isStatic = $isStatic;
//        $this->isConstructor = $isConstructor;
//        $this->isMethodChaining = $isMethodChaining;
    }

    public function getThrownTypes(): array
    {
        // TODO: Implement getThrownTypes() method.
    }

    public function setAssignment(Assignment $assignment)
    {
        $this->assignment = $assignment;
    }

    public function getSourceReference(): string
    {
        // TODO: Implement getSourceReference() method.
    }

    public function isSourceReferenceParameter(): bool
    {
        // TODO: Implement isSourceReferenceParameter() method.
    }

    public function getSourcePresenceCheckerReference(): ?PresenceCheck
    {
    }

    public function getSourceType(): Type
    {
        // TODO: Implement getSourceType() method.
    }

    public function createUniqueVarName(string $desiredName): string
    {
        // TODO: Implement createUniqueVarName() method.
    }

    public function getSourceLocalVarName(): ?string
    {
        return $this->sourceLocalVarName;
    }

    public function getSourceParameterName(): string
    {
        // TODO: Implement getSourceParameterName() method.
    }

    public function setSourceLocalVarName(?string $sourceLocalVarName)
    {
        // TODO: Implement setSourceLocalVarName() method.
    }

    public function getSourceLoopVarName(): ?string
    {
        // TODO: Implement getSourceLoopVarName() method.
    }

    public function setSourceLoopVarName(string $sourceLoopVarName): void
    {
        // TODO: Implement setSourceLoopVarName() method.
    }

    public function getType(): ?AssignmentType
    {
        // TODO: Implement getType() method.
    }

    public function isCallingUpdateMethod(): bool
    {
        // TODO: Implement isCallingUpdateMethod() method.
    }

    public function getImportTypes(): array
    {
        // TODO: Implement getImportTypes() method.
    }

    public function getTemplate(): string
    {
        return 'methodReference.twig';
    }

    /**
     * @param ParameterBinding[] $parameterBindings
     * @return static
     */
    public static function forMapperReference(Method $method, ?MapperReference $declaringMapper, array $parameterBindings = []): MethodReference
    {
        return new MethodReference(
            method: $method,
            declaringMapper: $declaringMapper,
            parameterBindings: $parameterBindings,
            providingParameter: null
        );
    }
}
