<?php

namespace org\octris\core\validate\type {
    /**
     * Validator for chaining multiple validation rules.
     *
     * @octdoc      c:type/chain
     * @copyright   copyright (c) 2010-2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class chain extends \org\octris\core\validate\type 
    /**/
    {
        /**
         * Constructor.
         *
         * @octdoc  m:chain/__construct
         * @param   array       $options        Options required by validator.
         */
        public function __construct(array $options)
        /**/
        {
            if (!isset($options['chain']) || !is_array($options['chain'])) {
                throw new \Exception('no chain provided');
            }
            
            parent::__construct($options);
        }
        
        /**
         * Validator implementation.
         *
         * @octdoc  m:chain/validate
         * @param   mixed       $value          Value to validate.
         * @return  bool                        Returns true if value is valid.
         */
        public function validate($value)
        /**/
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
