<?php

namespace BaptChau\Model\Constraints;

use BaptChau\Enum\ConstraintType;
use BaptChau\Model\AbstractConstraint;

class SwitchConstraint extends AbstractConstraint
{
    private bool $value;

    public function __construct(bool $value)
    {
        $this->value = $value;
        $this->type = ConstraintType::Switch;
    }

    public function getValue(): bool
    {
        return $this->value;
    }
}
