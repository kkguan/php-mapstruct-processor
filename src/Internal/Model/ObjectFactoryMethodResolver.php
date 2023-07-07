<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Method;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\SelectionParameters;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Selector\MethodSelectors;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Selector\SelectionCriteria;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\SourceMethod;

class ObjectFactoryMethodResolver
{
    public static function getMatchingFactoryMethods(Source\SourceMethod $method, Common\Type $alternativeTarget, Source\SelectionParameters $selectionParameters, MappingBuilderContext $ctx): ?array
    {
        $selectors = new MethodSelectors($ctx->getTypeFactory());

        $selectors->getMatchingMethods(
            $method,
            ObjectFactoryMethodResolver::getAllAvailableMethods($method, $ctx->getSourceModel()),
            [],
            $alternativeTarget,
            $alternativeTarget,
            SelectionCriteria::forFactoryMethods($selectionParameters)
        );
    }

    public static function getFactoryMethod(Method $method, Common\Type $alternativeTarget, SelectionParameters $selectionParameters, MappingBuilderContext $ctx): ?Source\SourceMethod
    {
        $matchingFactoryMethods = ObjectFactoryMethodResolver::getMatchingFactoryMethods($method, $alternativeTarget, $selectionParameters, $ctx);
    }

    /**
     * @param array<SourceMethod> $sourceModelMethods
     */
    public static function getAllAvailableMethods(Source\SourceMethod $method, array $sourceModelMethods)
    {
        $contextProvidedMethods = $method->getContextProvidedMethods();

        if (empty($contextProvidedMethods)) {
            return $sourceModelMethods;
        }

        $methodsProvidedByParams = $contextProvidedMethods->getAllProvidedMethodsInParameterOrder($method->getParameters());

        $availableMethods = [];
    }
}
