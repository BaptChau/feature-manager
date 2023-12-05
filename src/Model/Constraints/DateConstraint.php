<?php

namespace BaptChau\Model\Constraints;

use BaptChau\Enum\ConstraintType;
use BaptChau\Model\AbstractConstraint;

class DateConstraint extends AbstractConstraint
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
        $this->type = ConstraintType::Date;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
