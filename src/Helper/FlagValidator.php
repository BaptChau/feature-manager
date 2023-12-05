<?php

namespace BaptChau\Helper;
use BaptChau\Model\Flag;

class FlagValidator
{
    /**
     * Validation of a flag passed in params
     * 
     * @param array $flag flag to validate
     * @return boolean true if valid
     */
    public static function validateFlag(array $flag): bool
    {
        $flag = Flag::createFromArray($flag);

        if (trim($flag->getFlagName()) === "") {
            return false;
        }

        if ($flag->getDeleteDateFormat() < date("Y-m-d", strtotime("-7 days"))) {
            return false;
        }

        if (!ConstraintValidator::checkConstraints($flag->getConstraints())) {
            return false;
        }

        if (trim($flag->getDescription()) === "") {
            return false;
        }

        if (trim($flag->getAuthor()) === "") {
            return false;
        }

        return true;
    }
}