<?php

namespace org\octris\core\validate\type
    /****c* type/alphanum
     * NAME
     *      alphanum
     * FUNCTION
     *      validate string with characters 0-9a-zA-Z
     * COPYRIGHT
     *      copyright (c) 2008-2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */

    class alphanum extends \org\octris\core\validate\type {
        /****m* alphanum/validate
         * SYNOPSIS
         */
        public function validate($value)
        /*
         * FUNCTION
         *      validate an alphanum value
         * INPUTS
         *      * $value (mixed) -- value to validate
         * OUTPUTS
         *      (bool) -- returns true, if value is valid
         ****
         */
        {
            return ctype_alnum($value);
        }
    }
}
