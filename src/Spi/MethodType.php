<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Spi;

class MethodType
{
    /**
     * A JavaBeans getter method, e.g. {@code public String getName()}.
     */
    public const GETTER = 'getter';

    /**
     * A JavaBeans setter method, e.g. {@code public void setName(String name)}.
     */
    public const SETTER = 'setter';

    /**
     * An adder method, e.g. {@code public void addItem(String item)}.
     */
    public const ADDER = 'adder';

    /**
     * Any method which is neither a JavaBeans getter, setter nor an adder method.
     */
    public const OTHER = 'other';

    /**
     * A method to check whether a property is present, e.g. {@code public String hasName()}.
     */
    public const PRESENCE_CHECKER = 'presence_checker';
}
