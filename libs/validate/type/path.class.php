<?php

namespace org\octris\core\validate\path
    /****c* type/path
     * NAME
     *      path
     * FUNCTION
     *      validate if a string is a valid path
     * COPYRIGHT
     *      copyright (c) 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */

    class path extends \org\octris\core\validate\type {
        /****m* path/validate
         * SYNOPSIS
         */
        public function validate($value)
        /*
         * FUNCTION
         *      validate a path
         * INPUTS
         *      * $value (mixed) -- value to validate
         * OUTPUTS
         *      (bool) -- returns true, if value is valid
         ****
         */
        {
            return (is_dir($value));
        }
    }
}
