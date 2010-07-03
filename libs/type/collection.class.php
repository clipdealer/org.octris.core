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
        
        /****m* collection/getArrayCopy
         * SYNOPSIS
         */
        function getArrayCopy()
        /*
         * FUNCTION
         *      returns copy of data as PHP array
         * OUTPUTS
         *      (array) -- collection data
         ****
         */
        {
            return $this->data;
        }
        
        /****m* collection/offsetExists
         * SYNOPSIS
         */
        function offsetExists($offs)
        /*
         * FUNCTION
         *      whether a offset exists
         * INPUTS
         *      * $offs (string) -- offset to test
         * OUTPUTS
         *      (bool) -- returns true, if offset exists
         ****
         */
        {
            return (in_array($offs, $this->keys));
        }

        /****m* collection/offsetGet
         * SYNOPSIS
         */
        function offsetGet($offs)
        /*
         * FUNCTION
         *      offset to retrieve
         * INPUTS
         *      * $offs (string) -- offset to retrieve
         * OUTPUTS
         *      (mixed) -- array value for offset
         ****
         */
        {
            $idx = array_search($offs, $this->keys, true);
        
            return ($idx !== false ? $this->data[$this->keys[$idx]] : false);
        }

        /****m* collection/offsetSet
         * SYNOPSIS
         */
        function offsetSet($offs, $value)
        /*
         * FUNCTION
         *      offset to set
         * INPUTS
         *      * $offs (string) -- offset to set
         *      * $value (mixed) -- value for offset to set
         ****
         */
        {
            // is_null implements $...[] = ...
            if (!is_null($offs) && ($idx = array_search($offs, $this->keys, true)) !== false) {
                $this->data[$this->keys[$idx]] = $tmp;
            } else {
                $this->keys[]      = $offs;
                $this->data[$offs] = $tmp;
            }
        }
        
        /****m* collection/offsetUnset
         * SYNOPSIS
         */
        function offsetUnset($offs)
        /*
         * FUNCTION
         *      offset to unset
         * INPUTS
         *      * $offs (string) -- offset to unset
         ****
         */
        {
            $idx = array_search($offs, $this->keys, true);
        
            if ($idx !== false) {
                unset($this->keys[$idx]);
                unset($this->data[$offs]);
            }
        }

        /****m* collection/serialize
         * SYNOPSIS
         */
        function serialize()
        /*
         * FUNCTION
         *      when serializing collection
         * OUTPUTS
         *      (string) -- serialized collection data
         ****
         */
        {
            return serialize($this->data);
        }

        /****m* collection/unserialize
         * SYNOPSIS
         */
        function unserialize($data)
        /*
         * FUNCTION
         *      when collection data is unserialized
         * INPUTS
         *      * $data (string) -- serialized data to unserialize und pull into collection
         ****
         */
        {
            $this->__construct(unserialize($data));
        }

        /****m* collection/count
         * SYNOPSIS
         */
        function count()
        /*
         * FUNCTION
         *      count items in collection
         * OUTPUTS
         *      (int) -- items in collection
         ****
         */
        {
            return count($this->data);
        }
    }
}

