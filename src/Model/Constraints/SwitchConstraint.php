<?php

namespace BaptChau\Model\Constraints;

use BaptChau\Enum\ConstraintType;
use BaptChau\Exception\InvalidFlagException;
use BaptChau\Model\AbstractConstraint;
use DateTimeInterface;

/**
 * Manual on/off switch: false disables the flag whatever the other constraints say.
 */
class SwitchConstraint extends AbstractConstraint
{
    private bool $value;

    public function __construct(bool $value)
    {
        $this->value = $value;
        $this->type = ConstraintType::Switch;
    }

    public static function fromConfig(mixed $value): SwitchConstraint
    {
        if (!is_bool($value)) {
            throw new InvalidFlagException("Switch constraint must be true or false");
        }

        return new SwitchConstraint($value);
    }

    public function getValue(): bool
    {
        return $this->value;
    }

    public function isSatisfied(?string $subject, DateTimeInterface $now): bool
    {
        return $this->value;
    }
}
