<?php

namespace BaptChau\Model;

use BaptChau\Enum\ConstraintType;

abstract class AbstractConstraint
{
    protected ConstraintType $type;

    public function getConstraintType(): ConstraintType
    {
        return $this->type;
    }

    abstract public function getValue();
}
