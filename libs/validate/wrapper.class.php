<?php

namespace org\octris\core\validate {
    /****c* validate/wrapper
     * NAME
     *      wrapper
     * FUNCTION
     *      enable validation for arrays
     * COPYRIGHT
     *      copyright (c) 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald.lapp@gmail.com>
     ****
     */

    class wrapper extends \org\octris\core\type\collection {
        /****v* wrapper/$defaults
         * SYNOPSIS
         */
        protected $defaults = array(
            'isSet'     => true,
            'isValid'   => false,
            'isTainted' => true,
            'value'     => null,
            'tainted'   => null
        );
        /*
         * FUNCTION
         *      default values for a parameter
         ****
         */

        /****m* wrapper/__constructor
         * SYNOPSIS
         */
        public function __construct($source)
        /*
         * FUNCTION
         *      constructor
         ****
         */
        {
            if (($cnt = count($source)) > 0) {
                $this->keys = array_keys($source);
                $this->data = array();
                
                $me = $this;
                
                foreach ($source as $k => $v) {
                    $this->data[$k] = (object)$this->defaults;
                    $this->data[$k]->isSet   = true;
                    $this->data[$k]->tainted = $v;
                }
            } else {
                $this->data = array();
            }
        }

        /****m* wrapper/validate
         * SYNOPSIS
         */
        public function validate($name, $type, array $options = array())
        /*
         * FUNCTION
         *      validate and untained a value
         * INPUTS
         *      * $name (string) -- name of value to untaint
         *      * $type (string) -- type of value
         *      * $options (array) -- (optional) options for validating
         * OUTPUTS
         *      (bool) -- returns true, if validation succeededs
         ****
         */
        {
            if (($valid = isset($this->data[$name]))) {
                if ($this->data[$name]->isTainted) {
                    $instance = new $type($options);
                    $value    = $instance->preFilter($this->data[$name]->tainted);
                    
                    if (($valid = $instance->validate($value))) {
                        $this->data[$name]->value = $value;
                    }
                    
                    $this->data[$name]->isTainted = false;
                    $this->data[$name]->isSet     = true;
                    $this->data[$name]->isValid   = $valid;
                } else {
                    $valid = $this->data[$name]->isValid;
                }
            }
            
            return $valid;
        }

        /****m* wrapper/offsetGet
         * SYNOPSIS
         */
        public function offsetGet($offs)
        /*
         * FUNCTION
         *      return array entry of specified offset
         * INPUTS
         *      * $offs (string) -- offset
         * OUTPUTS
         *      (stdClass) -- value
         ****
         */
        {
            if (($idx = array_search($offs, $this->keys, true)) !== false) {
                $return = $this->data[$this->keys[$idx]];
            } else {
                $return = (object)$this->defaults;
                $return->isSet = false;
                
                parent::offsetSet($offs, $return);
            }
        
            return $return;
        }
            
        /****m* wrapper/offsetSet
         * SYNOPSIS
         */
        public function offsetSet($offs, $value)
        /*
         * FUNCTION
         *      overwrite collection's offsetSet to store value with meta data
         * INPUTS
         *      * $offs (mixed) -- offset to store value at
         *      * $value (mixed) -- value to store
         ****
         */
        {
            $me = $this;
            
            $tmp = (object)$this->defaults;
            $tmp->isSet     = true;
            $tmp->isValid   = true;
            $tmp->isTainted = false;
            $tmp->tainted   = $value;
            $tmp->value     = $value;
            
            parent::offsetSet($offs, $tmp);
        }
    }
}
