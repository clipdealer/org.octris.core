<?php

namespace org\octris\core\validate\type {
    /****c* type/xdigit
     * NAME
     *      xdigit
     * FUNCTION
     *      validate for hexadezimal values
     * COPYRIGHT
     *      copyright 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald.lapp@gmail.com>
     ****
     */

    class xdigit extends \org\octris\core\validate\type {
        /****m* xdigit/validate
         * SYNOPSIS
         */
        public function validate($value)
        /*
         * FUNCTION
         *      validate an xdigit value
         * INPUTS
         *      * $value (mixed) -- value to validate
         * OUTPUTS
         *      (bool) -- returns true, if value is valid
         ****
         */
        {
            return ctype_xdigit($value);
        }
    }
}
