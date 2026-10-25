<?php

namespace BaptChau\Model\Constraints;

use BaptChau\Enum\ConstraintType;
use BaptChau\Exception\InvalidFlagException;
use BaptChau\Model\AbstractConstraint;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;

/**
 * Flag is enabled between openingDate (included) and closingDate (excluded).
 * Both dates are optional.
 */
class DateConstraint extends AbstractConstraint
{
    private ?DateTimeImmutable $openingDate;
    private ?DateTimeImmutable $closingDate;

    public function __construct(?DateTimeImmutable $openingDate, ?DateTimeImmutable $closingDate)
    {
        if ($openingDate === null && $closingDate === null) {
            throw new InvalidFlagException("Date constraint needs an openingDate or a closingDate");
        }
        if ($openingDate !== null && $closingDate !== null && $closingDate <= $openingDate) {
            throw new InvalidFlagException("closingDate must be after openingDate");
        }

        $this->openingDate = $openingDate;
        $this->closingDate = $closingDate;
        $this->type = ConstraintType::Date;
    }

    public static function fromConfig(mixed $value): DateConstraint
    {
        if (!is_array($value)) {
            throw new InvalidFlagException("Date constraint must be a map with openingDate and/or closingDate");
        }

        return new DateConstraint(
            self::parseDate($value['openingDate'] ?? null, "openingDate"),
            self::parseDate($value['closingDate'] ?? null, "closingDate")
        );
    }

    public function getValue(): array
    {
        return [
            'openingDate' => $this->openingDate,
            'closingDate' => $this->closingDate,
        ];
    }

    public function getOpeningDate(): ?DateTimeImmutable
    {
        return $this->openingDate;
    }

    public function getClosingDate(): ?DateTimeImmutable
    {
        return $this->closingDate;
    }

    public function isSatisfied(?string $subject, DateTimeInterface $now): bool
    {
        if ($this->openingDate !== null && $now < $this->openingDate) {
            return false;
        }
        if ($this->closingDate !== null && $now >= $this->closingDate) {
            return false;
        }
        return true;
    }

    private static function parseDate(mixed $date, string $field): ?DateTimeImmutable
    {
        if ($date === null) {
            return null;
        }
        // yaml parses unquoted dates as timestamps
        if (is_int($date)) {
            return (new DateTimeImmutable())->setTimestamp($date);
        }
        if (!is_string($date) || trim($date) === "") {
            throw new InvalidFlagException("$field must be a date string");
        }

        try {
            return new DateTimeImmutable($date);
        } catch (Exception) {
            throw new InvalidFlagException("$field \"$date\" is not a valid date");
        }
    }
}
