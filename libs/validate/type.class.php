<?php

namespace org\octris\core\validate {
    /****c* validate/type
     * NAME
     *      type
     * FUNCTION
     *      base class for all validation types
     * COPYRIGHT
     *      copyright (c) 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */

    abstract class type {
        /****v* type/$options
         * SYNOPSIS
         */
        protected $options = array();
        /*
         * FUNCTION
         *      stores validation options
         ****
         */
    
        /****v* type/$pattern
         * SYNOPSIS
         */
        protected $pattern = '';
        /*
         * FUNCTION
         *      Validation pattern -- regular expression. If this is an
         *      empty string, the validation can only be performed server-
         *      side.
         ****
         */
        
        /****m* type/__construct
         * SYNOPSIS
         */
        function __construct(array $options = array())
        /*
         * FUNCTION
         *      constructor
         * INPUTS
         *      * $options (array) -- optional options
         ****
         */
        {
            $this->options = $options;
        }
    
        /****m* type/validate
         * SYNOPSIS
         */
        abstract function validate($value);
        /*
         * FUNCTION
         *      abstract methods must be implemented by subclasses
         ****
         */
    
        /****m* type/preFilter
         * SYNOPSIS
         */
        function preFilter($value)
        /*
         * FUNCTION
         *      pre filter values
         * INPUTS
         *      * $value (mixed) -- value to filter
         * OUTPUTS
         *      (mixed) -- filtered value
         ****
         */
        {
            // strip magic quotes, if enabled
            if (get_magic_quotes_gpc()) {
                $value = stripslashes($value);
            }

            // replace nullbytes
            $value = str_replace("\0", '', $value);
        
            return $value;
        }
    
        /****m* type/postValidate
         * SYNOPSIS
         */
        function postValidate($value)
        /*
         * FUNCTION
         *      
         * INPUTS
         *      
         * OUTPUTS
         *      
         ****
         */
        {
            return $value;
        }
    
        /****m* type/getOptions
         * SYNOPSIS
         */
        public function getOptions()
        /*
         * FUNCTION
         *      return the options, that where provided when constructing the object
         ****
         */
        {
            return $this->options;
        }
    
        /****m* type/getPattern
         * SYNOPSIS
         */
        public function getPattern()
        /*
         * FUNCTION
         *      returns a validation pattern.
         ****
         */
        {
            return $this->pattern;
        }
    }
}