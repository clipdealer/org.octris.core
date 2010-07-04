<?php

namespace org\octris\core\validate\type {
    /****c* type/alpha
     * NAME
     *      alpha
     * FUNCTION
     *      validate for string containing only a-zA-Z characters
     * COPYRIGHT
     *      copyright 2008-2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */

    class alpha extends \org\octris\core\validate\type {
        /****v* alpha/$pattern
         * SYNOPSIS
         */
        protected $pattern = '/^[a-zA-Z]+$/';
        /*
         * FUNCTION
         *      validation pattern
         ****
         */
    
        /****m* alpha/validate
         * SYNOPSIS
         */
        function validate($value)
        /*
         * FUNCTION
         *      validate an alpha value
         * INPUTS
         *      * $value (mixed) -- value to validate
         * OUTPUTS
         *      (bool) -- returns true, if value is valid
         ****
         */
        {
            return ctype_alpha($value);
        }
    }
}
