<?php

namespace BaptChau\Model\Constraints;

use BaptChau\Enum\ConstraintType;
use BaptChau\Model\AbstractConstraint;

class ListConstraint extends AbstractConstraint
{
    private array $value;

    public function __construct(array $list)
    {
        $this->value = $list;
        $this->type = ConstraintType::List;
    }

    public function getValue(): array
    {
        return $this->value;
    }
}
