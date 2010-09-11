<?php

namespace org\octris\core\validate\type {
    /****c* type/url
     * NAME
     *      url
     * FUNCTION
     *      validate URLs
     * COPYRIGHT
     *      copyright 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */

    class url extends \org\octris\core\validate\type {
        /****v* url/$pattern
         * SYNOPSIS
         */
        protected $pattern = "/^%s:\/\/(([a-z0-9\$\-\_\.\+\!\*'\(\)\,\;\?\&\=]|(\%[0-9a-f]{2}))+(\:([a-z0-9\$\-\_\.\+\!\*'\(\)\,\;\?\&\=]|(\%[0-9a-f]{2}))+)?\@)?((([a-z0-9]|([a-z0-9]([a-z0-9\-])*[a-z0-9]))\.)*([a-z]|([a-z][a-z0-9\-]*[a-z0-9]))|[0-9]{1,3}(\.[0-9]{1,3}){3})(\:[0-9]+)?(\/([a-z0-9\$\-\_\.\+\!\*'\(\)\,\;\:\@\&\=]|(\%[0-9a-f]{2}))*(\/([a-z0-9\$\-\_\.\+\!\*'\(\)\,\;\:\@\&\=]|(\%[0-9a-f]{2}))*)*(\?([a-z0-9\$\-\_\.\+\!\*'\(\)\,\;\:\@\&\=]|(\%[0-9a-f]{2}))*)?)?$/i";
        /*
         * FUNCTION
         *      validation pattern
         ****
         */
        
        /****v* url/$default_scheme
         * SYNOPSIS
         */
        protected $default_scheme = 'http://';
        /*
         * FUNCTION
         *      default scheme to use, if no one is provided
         ****
         */

        /****m* url/__construct
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
            if (!isset($options['schemes']) || !is_array($options['schemes'])) {
                $options['schemes'] = array('http', 'https');
            }
            
            if (isset($options['default_scheme']) && is_string($options['default_scheme'])) {
                $this->default_scheme = $options['default_scheme'];
            }
            
            parent::__construct($options);
        }

        /****m* url/preFilter
         * SYNOPSIS
         */
        public function preFilter($value)
        /*
         * FUNCTION
         *      overwrite preFilter of super class to add a default scheme, if no scheme is specified
         ****
         */
        {
            $value = parent::preFilter($value);

            if (trim($value) != '' && !preg_match('|^[^:]+://|', $value)) {
                $value = $this->default_scheme . $value;
            }

            return $value;
        }

        /****m* url/validate
         * SYNOPSIS
         */
        public function validate($value)
        /*
         * FUNCTION
         *      validate an URL
         * INPUTS
         *      * $value (mixed) -- value to validate
         * OUTPUTS
         *      (bool) -- returns true, if value is valid
         ****
         */
        {
            $pattern = sprintf(
                $this->pattern,
                (count($options['schemes']) > 0
                 ? '(' . implode('|', $options['scheme']) . ')'
                 : '(.+)')
            );
                        
            return preg_match($pattern, $value);
        }
    }
}
