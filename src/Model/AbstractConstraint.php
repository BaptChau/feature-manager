<?php

namespace BaptChau\Model;

use BaptChau\Enum\ConstraintType;
use BaptChau\Exception\InvalidFlagException;
use BaptChau\Model\Constraints\DateConstraint;
use BaptChau\Model\Constraints\ListConstraint;
use BaptChau\Model\Constraints\SwitchConstraint;
use DateTimeInterface;

abstract class AbstractConstraint
{
    protected ConstraintType $type;

    public function getConstraintType(): ConstraintType
    {
        return $this->type;
    }

    abstract public function getValue();

    /**
     * Check if the constraint allows the flag to be enabled
     *
     * @param string|null $subject identifier checked against list constraints (user id, email...)
     * @param DateTimeInterface $now
     * @return bool
     */
    abstract public function isSatisfied(?string $subject, DateTimeInterface $now): bool;

    /**
     * Build a constraint from its yaml key and value
     *
     * @throws InvalidFlagException
     */
    public static function create(string $type, mixed $value): AbstractConstraint
    {
        return match ($type) {
            "date" => DateConstraint::fromConfig($value),
            "list" => ListConstraint::fromConfig($value),
            "switch" => SwitchConstraint::fromConfig($value),
            default => throw new InvalidFlagException("Unknown constraint type \"$type\""),
        };
    }
}
