<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Selector;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\Type;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Common\TypeFactory;
use Kkguan\PHPMapstruct\Processor\Internal\Model\Source\Method;

/**
 * Applies all known MethodSelectors in order.
 */
class MethodSelectors
{
    /**
     * @var MethodSelectorInterface[]
     */
    private array $selectors;

    public function __construct(TypeFactory $typeFactory)
    {
        $this->selectors = [
            new MethodFamilySelector(),
            new TypeSelector($typeFactory),
            new QualifierSelector(),
            new TargetTypeSelector($typeFactory),
            new InheritanceSelector(),
            new CreateOrUpdateSelector(),
            new SourceRhsSelector(),
            new FactoryParameterSelector(),
        ];
    }

    public function getMatchingMethods(Method $mappingMethod, ?array $methods, Type $source, Type $target, Type $target1, SelectionCriteria $selectionCriteria)
    {
        // TODO:待实现
        /** @var SelectedMethod[] $candidates */
        $candidates = [];
        foreach ($methods as $method) {
            $candidates[] = new SelectedMethod($method);
        }

        foreach ($this->selectors as $selector) {
            $candidates = $selector->getMatchingMethods($mappingMethod, $candidates, [$source], $target, $target1, $selectionCriteria);
        }

        return $candidates;
    }
}
