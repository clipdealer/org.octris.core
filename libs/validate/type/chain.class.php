<?php

namespace org\octris\core\validate\type {
    /****c* type/chain
     * NAME
     *      chain
     * FUNCTION
     *      implements validation chaining -- chaining of multiple validation rules.
     * COPYRIGHT
     *      copyright (c) 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */

    class chain {
        /****m* chain/__construct
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
            if (!isset($options['chain']) || !is_array($options['chain'])) {
                throw new \Exception('no chain provided');
            }
            
            parent::__construct($options);
        }
        
        /****m* chain/validate
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
            $return = true;
            
            foreach ($this->options['chain'] as $item) {
                if (isset($item['type'])) {
                    $t = strtolower($item['type']);
                    $o = (isset($item['options']) && is_array($item['options'])
                            ? $item['options']
                            : array());
                    
                    $instance = new $t($o);
                    $value    = $instance->preFilter($value);
                    
                    if (!($return = $instance->validate($value))) {
                        break;
                    }
                }
            }
            
            return $return;
        }
    }
}
