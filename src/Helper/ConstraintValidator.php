<?php

namespace BaptChau\Helper;

use BaptChau\Model\Constraint;
use BaptChau\Model\Constraints\DateConstraint;
use BaptChau\Model\Constraints\ListConstraint;
use BaptChau\Model\Constraints\SwitchConstraint;
use DateTime;

class ConstraintValidator
{
    /**
     * Check an array of constraint
     * 
     * @param array $constriants
     * @return bool
     */
    public static function checkConstraints(array $constraints): bool
    {
        foreach ($constraints as $type => $value) {
            $result = false;
            $result = match ($type) {
                "date" => ConstraintValidator::checkDateConstraint($value),
                "list" => ConstraintValidator::checkListConstraint($value),
                "switch" => ConstraintValidator::checkSwitchConstraint($value)
            };
            if (!$result) {
                return false;
            }
        }
        return true;
    }

    public static function checkDateConstraint(array $constraintValue): bool
    {
        $openingDate = $constraintValue['openingDate'] ?? null;
        $closingDate = $constraintValue['closingDate'] ?? null;

        if ($openingDate !== null) {
            $openingDateTime = new DateTime($openingDate);
        }
        if ($closingDate !== null) {
            $closingDateTime = new DateTime($closingDate);
        }

        if ($openingDateTime && $closingDateTime) {
            return $closingDateTime > $openingDateTime;
        } elseif ($openingDateTime && !$closingDateTime) {
            return $openingDate > strtotime("now");
        } elseif (!$openingDateTime && $closingDateTime) {
            return $closingDateTime < strtotime("now");
        }

        return false;
    }

    public static function checkListConstraint(array $constraintValue): bool
    {
        return !empty($constraintValue);
    }

    public static function checkSwitchConstraint(bool $constraintValue): bool
    {
        return !$constraintValue;
    }
}