<?php

namespace Content\Utilities;

class Validator
{
    /**
     * Set value to null if value is empty
     * @param mixed $variable This should be the variable you are checking if it is empty
     * @return mixed Returns either NULL or the original variable
     */
    public static function setNullOnEmpty($variable)
    {
        if (empty(trim($variable)) || (is_numeric($variable) && floatval($variable) == 0)) {
            return null;
        }
        return $variable;
    }
    
    /**
     * Set value to 0 if value is empty
     * @param mixed $variable This should be the variable you are checking if it is empty
     * @return mixed Returns either 0 or the original variable
     */
    public static function setZeroOnEmpty($variable)
    {
        if (empty(trim($variable)) || (is_numeric($variable) && floatval($variable) == 0)) {
            return 0;
        }
        return $variable;
    }
}
