<?php

namespace org\octris\core\validate {
    /****c* validate/wrapper
     * NAME
     *      wrapper
     * FUNCTION
     *      enable validation for arrays
     * COPYRIGHT
     *      copyright (c) 2006-2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald.lapp@gmail.com>
     ****
     */

    final class wrapper extends \org\octris\core\type\collection {
        /****v* wrapper/$defaults
         * SYNOPSIS
         */
        protected $defaults = array(
            'isSet'         => true,
            'isValid'       => false,
            'isValidated'   => false,
            'value'         => null,
            'unsanitized'   => null
        );
        /*
         * FUNCTION
         *      default values for a parameter
         ****
         */

        /****m* wrapper/__constructor
         * SYNOPSIS
         */
        function __construct($source)
        /*
         * FUNCTION
         *      constructor
         ****
         */
        {
            if (($cnt = count($source)) > 0) {
                $this->keys = array_keys($source);
                $this->data = array();
                
                foreach ($source as $k => $v) {
                    $this->data[$k] = (object)$this->defaults;
                    $this->data[$k]->isSet       = true;
                    $this->data[$k]->unsanitized = $v;
                }
            } else {
                $this->data = array();
            }
        }

        /****m* wrapper/offsetSet
         * SYNOPSIS
         */
        function offsetSet($offs, $value)
        /*
         * FUNCTION
         *      overwrite collection's offsetSet to store value with meta data
         * INPUTS
         *      * $offs (mixed) -- offset to store value at
         *      * $value (mixed) -- value to store
         ****
         */
        {
            $tmp = (object)$this->defaults;
            $tmp->isSet       = true;
            $tmp->isValid     = true;
            $tmp->isValidated = true;
            $tmp->unsanitized = $value;
            $tmp->value       = $value;
            
            parent::offsetSet($offs, $tmp);
        }
    }
}

?>