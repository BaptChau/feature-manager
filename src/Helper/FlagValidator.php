<?php

namespace BaptChau\Helper;

use BaptChau\Exception\InvalidFlagException;
use BaptChau\Model\Flag;

class FlagValidator
{
    /**
     * Validation of a flag passed in params
     *
     * Only checks the structure of the flag: an expired or disabled flag is still valid.
     *
     * @param array $flag flag to validate
     * @return boolean true if valid
     */
    public static function validateFlag(array $flag): bool
    {
        try {
            Flag::createFromArray($flag);
        } catch (InvalidFlagException) {
            return false;
        }

        return true;
    }
}
