<?php

namespace org\octris\core\validate\type {
    /****c* validate/print
     * NAME
     *      print
     * FUNCTION
     *      validate for printable values
     * COPYRIGHT
     *      copyright 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */

    class print extends \org\octris\core\validate\type {
        /****m* print/validate
         * SYNOPSIS
         */
        public function validate($value)
        /*
         * FUNCTION
         *      validate an print value
         * INPUTS
         *      * $value (mixed) -- value to validate
         * OUTPUTS
         *      (bool) -- returns true, if value is valid
         ****
         */
        {
        	return !preg_match('/[\f\n\r\t]/', $value);
        }
    }
}
