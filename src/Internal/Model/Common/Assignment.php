<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Common;

interface Assignment
{
    /**
     * returns all types required as import by the assignment statement.
     *
     * @return null|Type[] imported types
     */
    public function getImportTypes(): ?array;

    /**
     * returns all types exception types thrown by this assignment.
     *
     * @return null|\Exception[] thrown
     */
    public function getThrownTypes(): array;

    /**
     * An assignment in itself can wrap another assignment. E.g.:
     * <ul>
     * <li>a MethodReference can wrap a TypeConversion, another MethodReference and ultimately a Simple</li>
     * <li>a TypeConversion can wrap a MethodReference, and ultimately a Simple</li>
     * </ul>.
     *
     * @param assignment the assignment to set
     */
    public function setAssignment(Assignment $assignment);

    /**
     * the source reference being a source-getter, a constant, nested method call, etc.
     *
     * @return source reference
     */
    public function getSourceReference(): string;

    /**
     * @return true when the source reference is the source parameter (and not a property of the source parameter type)
     */
    public function isSourceReferenceParameter(): bool;

    /**
     * the source presence checker reference.
     *
     * @return source reference
     */
    public function getSourcePresenceCheckerReference(): ?PresenceCheck;

    /**
     * the source type used in the matching process.
     *
     * @return source type (can be null)
     */
    public function getSourceType(): Type;

    /**
     * Creates an unique safe (local) variable name.
     *
     * @param desiredName the desired name
     *
     * @return the desired name, made unique in the scope of the bean mapping
     */
    public function createUniqueVarName(string $desiredName): string;

    /**
     * See {@link #setSourceLocalVarName(java.lang.string)}.
     *
     * @return local variable name (can be null if not set)
     */
    public function getSourceLocalVarName(): ?string;

    /**
     * Returns the source parameter name, to which this assignment applies. Note: the source parameter itself might
     * be mapped by this assignment, or one of its properties.
     *
     * @return the source parameter name
     */
    public function getSourceParameterName(): string;

    /**
     * Replaces the sourceReference at the call site in the assignment in the template with this sourceLocalVarName.
     * The sourceLocalVarName can subsequently be used for e.g. null checking.
     *
     * @param sourceLocalVarName source local variable name
     */
    public function setSourceLocalVarName(?string $sourceLocalVarName);

    /**
     * See {@link #getSourceLoopVarName()} (java.lang.string) }.
     *
     * @return loop variable (can be null if not set)
     */
    public function getSourceLoopVarName(): ?string;

    /**
     * Replaces the sourceLocalVar or sourceReference at the call site in the assignment in the template with this
     * sourceLoopVarName.
     * The sourceLocalVar can subsequently be used for e.g. null checking.
     *
     * @param sourceLoopVarName loop variable
     */
    public function setSourceLoopVarName(string $sourceLoopVarName): void;

    /**
     * Returns whether the type of assignment.
     *
     * @return {@link AssignmentType}
     */
    public function getType(): ?AssignmentType;

    public function getTemplate(): string;

    public function isCallingUpdateMethod(): bool;
}
