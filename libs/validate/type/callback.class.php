<?php

namespace org\octris\core\validate\type {
    /****c* type/callback
     * NAME
     *      callback
     * FUNCTION
     *      with a specified callback function a user defined validation process
     *      can be used.
     * COPYRIGHT
     *      copyright (c) 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */

    class callback {
        /****m* callback/__construct
         * SYNOPSIS
         */
        public function __construct(array $options)
        /*
         * FUNCTION
         *      constructor
         * INPUTS
         *      * $options (array) -- options for validator
         ****
         */
        {
            if (!isset($options['callback']) || !is_callable($options['callback'])) {
                throw new \Exception('valid callback is required');
            }
            
            parent::__construct($options);
        }
        
        /****m* callback/validate
         * SYNOPSIS
         */
        public function validate($value)
        /*
         * FUNCTION
         *      validate value
         * INPUTS
         *      * $value (mixed) -- value to validate
         * OUTPUTS
         *      (bool) -- returns true, if value is valid
         ****
         */
        {
            return !!$this->options['callback']($value);
        }
    }
}