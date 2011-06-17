<?php

namespace org\octris\core\type\collection {
    /**
     * Convert an object which implements the "getArrayCopy" method to an array. If an array is specified as first
     * parameter, the array is returned without any change.
     *
     * @octdoc  m:collection/normalize
     * @param   mixed       $p                      Either an array or an object which implements the getArrayCopy method.
     * @return  array|bool                          Array or false, if parameter could not be converted to array.
     */
    function normalize($p)
    /**/
    {
        if (is_object($p) && method_exists($value, 'getArrayCopy')) {
            $p = $p->getArrayCopy();
        } elseif (!is_array($p)) {
            $p = false;
        }
        
        return $p;
    }
    
    /**
     * Return keys of array / collection.
     *
     * @octdoc  m:collection/keys
     * @param   mixed       $p                      Either an array or an object which implements the getArrayCopy method.
     * @return  array|bool                          Array of stored keys or false.
     */
    function keys($p)
    /**/
    {
        return (($p = normalize($p)) ? array_keys($p) : false);
    }
    
    /**
     * Return values of array / collection.
     *
     * @octdoc  m:collection/values
     * @param   mixed       $p                      Either an array or an object which implements the getArrayCopy method.
     * @return  array|bool                          Array of stored keys or false.
     */
    function values($p)
    /**/
    {
        return (($p = normalize($p)) ? array_values($p) : false);
    }
        
    /**
     * Merge multiple arrays / collections.
     *
     * @octdoc  m:collection/merge
     * @param   mixed       $arg1, ...              Array(s) / collection(s) to merge.
     * @return  array                               Merged data.
     */
    function merge($arg1)
    /**/
    {
        $args = func_get_args();
        array_shift($args);
        
        for ($i = 0, $cnt = count($args); $i < $cnt; ++$i) {
            if (($arg = normalize($args[$i]))) {
                $arg1 = array_merge($arg1, $arg);
            }
        }
        
        return $arg1;
    }
    
    /**
     * Flatten a array / collection. Convert a (nested) structure into a flat array with expanded keys
     *
     * @octdoc  m:collection/flatten
     * @param   mixed       $p                      Either an array or an object which implements the getArrayCopy method.
     * @param   string      $sep                    Optional separator for expanding keys.
     * @return  array|bool                          Flattened structure or false, if input could not be processed.
     */
    function flatten($p, $sep = '.')
    /**/
    {
        if (!($p = normalize($p))) {
            return false;
        }
        
        $tmp = array();

        $array = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($p), \RecursiveIteratorIterator::SELF_FIRST);
        $d = 0;

        $property = array();

        foreach ($array as $k => $v) {
            if (!is_int($k)) {
                if ($d > $array->getDepth()) {
                    array_splice($property, $array->getDepth());
                }

                $property[$array->getDepth()] = $k;

                $d = $array->getDepth();
            }

            if (is_int($k)) {
                $tmp[implode($sep, $property)][] = $v;
            } elseif (!is_array($v)) {
                $tmp[implode($sep, $property)] = $v;
            }
        }

        return $tmp;
    }

    /**
     * Deflatten a flat array / collection.
     *
     * @octdoc  m:collection/deflatten
     * @param   mixed       $p                      Either an array or an object which implements the getArrayCopy method.
     * @param   string      $sep                    Optional separator for expanding keys.
     * @return  array|bool                          Deflattened collection or false if input could not be deflattened.
     */
    function deflatten($p, $sep = '.')
    /**/
    {
        if (!($p = normalize($p))) {
            return false;
        }
        
        $tmp = array();

        foreach ($p as $k => $v) {
            $key  = explode($sep, $k);
            $ref =& $tmp;

            foreach ($key as $part) {
                if (!isset($ref[$part])) {
                    $ref[$part] = array();
                }

                $ref =& $ref[$part];
            }

            $ref = $v;
        }

        return $tmp;
    }    
}

namespace org\octris\core\type {
    /**
     * Collection type. Implements special access on array objects.
     *
     * @octdoc      c:type/collection
     * @copyright   copyright (c) 2010-2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class collection implements \Iterator, \SeekableIterator, \ArrayAccess, \Serializable, \Countable
    /**/
    {
        /**
         * Collection data.
         *
         * @octdoc  v:collection/$data
         * @var     array
         */
        protected $data = array();
        /**/

        /**
         * Keys of collection data.
         *
         * @octdoc  v:collection/$keys
         * @var     array
         */
        protected $keys = array();
        /**/

        /**
         * Position of iterator.
         *
         * @octdoc  v:collection/$position
         * @var     int
         */
        protected $position = 0;
        /**/

        /**
         * Number of items in collection.
         *
         * @octdoc  v:collection/$cnt
         * @var     int
         */
        protected $cnt = 0;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:collection/__construct
         * @param   mixed       $value      Optional value to initialize collection with.
         */
        public function __construct($value = null)
        /**/
        {
            if (is_null($value)) {
                // initialize empty array if no value is specified
                $value = array();
            } elseif (is_scalar($value)) {
                // a scalar will be splitted into it's character, UTF-8 safe.
                $value = \org\octris\core\type\string\str_split((string)$value, 1);
            } elseif (is_object($value) && method_exists($value, 'getArrayCopy')) {
                // an object which proved the getArrayCopy method
                $value = $value->getArrayCopy();
            } elseif (!is_array($value)) {
                throw new Exception('don\'t know how to handle parameter of type "' . gettype($array) . '"');
            }
        
            $this->keys = array_keys($value);
            $this->data = $value;
            $this->cnt  = count($this->keys);
        }

        /** Iterator **/
        
        /**
         * Return the current element.
         *
         * @octdoc  m:collection/current
         * @return  mixed                  Current element.
         */
        public function current()
        /**/
        {
            return $this->data[$this->position];
        }
    
        /**
         * Return the key of the current element.
         *
         * @octdoc  m:collection/key
         * @return  mixed                   Key of current element.
         */
        public function key()
        /**/
        {
            return $this->keys[$this->position];
        }

        /**
         * Move forward to next element.
         *
         * @octdoc  m:collection/next
         */
        public function next()
        /**/
        {
            ++$this->position;
        }
        
        /**
         * Rewind the Iterator to the first element.
         *
         * @octdoc  m:collection/rewind
         */
        public function rewind()
        /**/
        {
            $this->position = 0;
        }
    
        /**
         * Checks if current position is valid.
         *
         * @octdoc  m:collection/valid
         * @return  bool                    Returns true, if element at position exists.
         */
        public function valid()
        /**/
        {
            return ($this->position < $this->cnt);
        }
    
        /** SeekableIterator **/
        
        /**
         * Seeks to a position.
         *
         * @octdoc  m:collection/seek
         * @param   int     $position       Position to seek to.
         */
        public function seek($position)
        /**/
        {
            $this->position = $position;
        }
    
        /** ArrayAccess **/
    
        /**
         * Whether a specified offset exists in collection data.
         *
         * @octdoc  m:collection/offsetExists
         * @param   string      $offs       Offset to test.
         * @return  bool                    Returns true if offset exists.
         */
        public function offsetExists($offs)
        /**/
        {
            return (in_array($offs, $this->keys, true));
        }

        /**
         * Return data for specified offset from collection.
         *
         * @octdoc  m:collection/offsetGet
         * @param   string      $offs       Offset to retrieve.
         * @return  mixed                   Value stored at specified offset or 'false'.
         */
        public function offsetGet($offs)
        /**/
        {
            $idx = array_search($offs, $this->keys, true);
        
            return ($idx !== false ? $this->data[$this->keys[$idx]] : false);
        }

        /**
         * Set value in collection at specified offset.
         *
         * @octdoc  m:collection/offsetSet
         * @param   string      $offs       Offset to set value at.
         * @param   mixed       $value      Value to set at offset.
         */
        public function offsetSet($offs, $value)
        /**/
        {
            // is_null implements $...[] = ...
            if (!is_null($offs) && ($idx = array_search($offs, $this->keys, true)) !== false) {
                $this->data[$this->keys[$idx]] = $value;
            } else {
                $this->keys[]      = $offs;
                $this->data[$offs] = $value;
            }
        }

        /**
         * Unset data in collection at specified offset.
         *
         * @octdoc  m:collection/offsetUnset
         * @param   string      $offs       Offset to unset.
         */
        public function offsetUnset($offs)
        /**/
        {
            $idx = array_search($offs, $this->keys, true);
        
            if ($idx !== false) {
                unset($this->keys[$idx]);
                unset($this->data[$offs]);
            }
        }

        /** Serializable **/

        /**
         * Implements serialization of collection data.
         *
         * @octdoc  m:collection/serialize
         * @return  string                  Serialized collection data.
         */
        public function serialize()
        /**/
        {
            return serialize($this->data);
        }

        /**
         * Implements unserialization of collection data.
         *
         * @octdoc  m:collection/unserialize
         * @param   string      $data       Serialized data to unserialize and push into collection.
         */
        public function unserialize($data)
        /**/
        {
            $this->__construct(unserialize($data));
        }

        /** Countable **/

        /**
         * Returns number of items stored in collection.
         *
         * @octdoc  m:collection/count
         * @return  int                 Number of items in collection.
         */
        public function count()
        /**/
        {
            return $this->cnt;
        }

        /** Special collection functionality **/

        /**
         * Returns copy of stored data as PHP array.
         *
         * @octdoc  m:collection/getArrayCopy
         * @return  array               Data stored in collection
         */
        public function getArrayCopy()
        /**/
        {
            return $this->data;
        }

        /**
         * Rename keys of collection but preserve the ordering of the collection.
         *
         * @octdoc  m:collection/keyrename
         * @param   array               $map                Map of origin name to new name.
         */
        public function keyrename($map)
        /**/
        {
            $this->data = array_combine(array_map(function($v) use ($map) {
                return (isset($map[$v])
                        ? $map[$v]
                        : $v);
            }, array_keys($this->data)), array_values($this->data));
            
            $this->keys = array_keys($this->data);
        }

        /**
         * Sets defaults for collection. Values are only set, if the keys of the values are not already available 
         * in collection.
         *
         * @octdoc  m:collection/defaults
         * @param   mixed       $value      Value(s) to set as default(s).
         */
        public function defaults($value)
        /**/
        {
            if (is_array($value)) {
                $this->data = array_merge($value, $this->data);
            } elseif (is_object($value)) {
                if (($value instanceof collection) || ($value instanceof collection\Iterator) || ($value instanceof \ArrayIterator)) {
                    $value = $value->getArrayCopy();
                } else {
                    $value = (array)$value;
                }

                $this->data = array_merge($value, $this->data);
            }
        }
    }
}

