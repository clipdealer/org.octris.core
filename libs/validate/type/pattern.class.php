<?php

namespace org\octris\core\validate\type {
    /****c* type/pattern
     * NAME
     *      pattern
     * FUNCTION
     *      pattern validation
     * COPYRIGHT
     *      copyright (c) 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */

    class pattern extends \org\octris\core\validate\type {
        /****m* pattern/__construct
         * SYNOPSIS
         */
        public function __construct(array $options = array())
        /*
         * FUNCTION
         *      constructor
         * INPUTS
         *      * $options (array) -- optional options
         ****
         */
        {
            if (!isset($options['pattern'])) {
                throw new \Exception('no pattern provided');
            }
            
            parent::__construct($options);
        }

        /****m* pattern/validate
         * SYNOPSIS
         */
        public function validate($value)
        /*
         * FUNCTION
         *      validate an pattern value
         * INPUTS
         *      * $value (mixed) -- value to validate
         * OUTPUTS
         *      (bool) -- returns true, if value is valid
         ****
         */
        {
            return preg_match($this->options['pattern'], $value);
        }
    }
}
