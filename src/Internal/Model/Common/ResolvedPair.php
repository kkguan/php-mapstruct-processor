<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Model\Common;

class ResolvedPair
{
    public function __construct(private Type $parameter, private Type $match)
    {
    }

    public function getParameter(): Type
    {
        return $this->parameter;
    }

    public function setParameter(Type $parameter): ResolvedPair
    {
        $this->parameter = $parameter;
        return $this;
    }

    public function getMatch(): Type
    {
        return $this->match;
    }

    public function setMatch(Type $match): ResolvedPair
    {
        $this->match = $match;
        return $this;
    }
}
