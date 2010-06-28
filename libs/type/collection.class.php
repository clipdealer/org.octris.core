<?php

namespace org\octris\core\type {
    /****c* type/collection
     * NAME
     *      collection
     * FUNCTION
     *      collection type -- implements special access on array
     *      objects
     * COPYRIGHT
     *      copyright (c) 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */

    class collection implements \IteratorAggregate, \ArrayAccess, \Serializable, \Countable {
        /****v* collection/$data
         * SYNOPSIS
         */
        protected $data = array();
        /*
         * FUNCTION
         *      collection data
         ****
         */

        /****v* collection/$keys
         * SYNOPSIS
         */
        protected $keys = array();
        /*
         * FUNCTION
         *      parameter names
         ****
         */
        
        /****m* collection/__construct
         * SYNOPSIS
         */
        function __construct($array = null)
        /*
         * FUNCTION
         *      constructor
         * INPUTS
         *      * $array (mixed) -- (optional) array to construct
         ****
         */
        {
            if (is_null($array)) {
                $array = array();
            } elseif (is_scalar($array)) {
                // a scalar will be splitted into bytes
                $array = str_split((string)$array, 1);
            } elseif (is_object($array)) {
                if (($array instanceof collection) || ($array instanceof collection\Iterator) || ($array instanceof \ArrayIterator)) {
                    $array = $array->getArrayCopy();
                } else {
                    $array = (array)$array;
                }
            } elseif (!is_array($array)) {
                throw new Exception('don\'t know how to handle parameter of type "' . gettype($array) . '"');
            }
        
            $this->keys = array_keys($array);
            $this->data = $array;
        }
    
        /****m* collection/getIterator
         * SYNOPSIS
         */
        function getIterator()
        /*
         * FUNCTION
         *      returns iterator object for collection
         * OUTPUTS
         *      (iterator) -- iterator object
         ****
         */
        {
            return new \ArrayIterator($this->data);
        }
            
        function offsetExists($offs) {
            return (in_array($offs, $this->keys));
        }
            
        function offsetGet($offs) {
            $idx = array_search($offs, $this->keys, true);
        
            return ($idx !== false ? $this->data[$this->keys[$idx]] : false);
        }
            
        function offsetSet($offs, $value) {
            // is_null implements $...[] = ...
            if (!is_null($offs) && ($idx = array_search($offs, $this->keys, true)) !== false) {
                $this->data[$this->keys[$idx]] = $tmp;
            } else {
                $this->keys[]      = $offs;
                $this->data[$offs] = $tmp;
            }
        }
        
        function offsetUnset($offs) {
            $idx = array_search($offs, $this->keys, true);
        
            if ($idx !== false) {
                unset($this->keys[$idx]);
                unset($this->data[$offs]);
            }
        }
            
        function serialize() {
            return serialize($this->data);
        }
            
        function unserialize($data) {
            $this->__construct($data);
        }
            
        function count() {
            return count($this->data);
        }
    }
}

