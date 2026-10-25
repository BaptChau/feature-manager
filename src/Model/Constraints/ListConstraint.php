<?php

namespace BaptChau\Model\Constraints;

use BaptChau\Enum\ConstraintType;
use BaptChau\Exception\InvalidFlagException;
use BaptChau\Model\AbstractConstraint;
use DateTimeInterface;

/**
 * Flag is enabled only for the subjects (user id, email...) present in the list.
 */
class ListConstraint extends AbstractConstraint
{
    private array $value;

    public function __construct(array $list)
    {
        if (empty($list)) {
            throw new InvalidFlagException("List constraint can't be empty");
        }

        $this->value = array_map('strval', array_values($list));
        $this->type = ConstraintType::List;
    }

    public static function fromConfig(mixed $value): ListConstraint
    {
        if (!is_array($value)) {
            throw new InvalidFlagException("List constraint must be a list");
        }
        foreach ($value as $item) {
            if (!is_scalar($item)) {
                throw new InvalidFlagException("List constraint values must be scalars");
            }
        }

        return new ListConstraint($value);
    }

    public function getValue(): array
    {
        return $this->value;
    }

    public function isSatisfied(?string $subject, DateTimeInterface $now): bool
    {
        return $subject !== null && in_array($subject, $this->value, true);
    }
}
