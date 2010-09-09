<?php

namespace org\octris\core\validate\type {
    /****c* type/hash
     * NAME
     *      hash
     * FUNCTION
     *      hash value validation
     * COPYRIGHT
     *      copyright (c) 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */

    class hash extends \org\octris\core\validate\type\xdigit {
        /****m* hash/validate
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
            $len = (isset($this->options['length'])
                    ? $this->options['length']
                    : 32);
            
            return (parent::validate($value) && strlen($value) == $len);
        }
    }
}