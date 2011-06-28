<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\type\collection {
    /**
     * Normalize an object which extens ArrayObject or ArrayIterator, returns an array.
     *
     * @octdoc  m:collection/normalize
     * @param   mixed       $p                      Either an array or an object which implements the getArrayCopy method.
     * @return  array|bool                          Array or false, if parameter could not be converted to array.
     */
    function normalize($p)
    /**/
    {
        if (is_object($p) && ($p instanceof ArrayObject || $p instanceof ArrayIterator)) {
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
     * Merge multiple arrays / collections. The function returns either an array or an collection depending on the type of the 
     * first argument.
     *
     * @octdoc  m:collection/merge
     * @param   mixed       $arg1, ...                              Array(s) / collection(s) to merge.
     * @return  array|\org\octris\core\type\collection|bool         Merged array data or false.
     */
    function merge($arg1)
    /**/
    {
        $is_collection = (is_object($arg1) && $arg1 instanceof \org\octris\core\type\collection);
        
        if (!($arg1 = normalize($arg1))) {
            return false;
        }
        
        $args = func_get_args();
        array_shift($args);
        
        for ($i = 0, $cnt = count($args); $i < $cnt; ++$i) {
            if (($arg = normalize($args[$i]))) {
                $arg1 = array_merge($arg1, $arg);
            }
        }
        
        if ($is_collection) {
            $arg1 = new \org\octris\core\type\collection($arg1);
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
        $is_collection = (is_object($p) && $p instanceof \org\octris\core\type\collection);

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

        if ($is_collection) {
            $p = new \org\octris\core\type\collection($p);
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
        $is_collection = (is_object($p) && $p instanceof \org\octris\core\type\collection);

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

        if ($is_collection) {
            $p = new \org\octris\core\type\collection($p);
        }

        return $tmp;
    }
    
    /**
     * Applies the callback to the elements of the given arrays.
     *
     * @octdoc  f:collection/map
     * @param   callback    $cb                 Callback to apply to each element.
     * @param   mixed       $arg1, ...          The input array(s), ArrayObject(s) and / or collection(s).
     * @return  array                           Returns an array containing all the elements of arg1 after applying the
     *                                          callback function to each one.
     */
    function map($cb, $arg1)
    /**/
    {
        $args = func_get_args();
        array_shift($args);
        $cnt = count($args);

        $is_collection = (is_object($arg1) && $arg1 instanceof \org\octris\core\type\collection);

        $data = array();
        $next = function() use (&$args, $cnt) {
            $return = array();
            $valid  = false;

            for ($i = 0; $i < $cnt; ++$i) {
                if (list($k, $v) = each($args[$i])) {
                    $return[] = $v;
                    $valid = true;
                } else {
                    $return[] = null;
                }
            }

            return ($valid ? $return : false);
        };

        while ($tmp = $next()) {
            $data[] = call_user_func_array($cb, $tmp);
        }

        if ($is_collection) {
            $data = new \org\octris\core\type\collection($data);
        }

        return $data;
    }
    
    /**
     * Apply a user function to every member of an array. 
     *
     * @octdoc  f:collection/walk
     * @param   mixed       $arg                The input array, ArrayObject or collection.
     * @param   callback    $cb                 Callback to apply to each element.
     * @param   mixed       $userdata           Optional userdata parameter will be passed as the third parameter to the 
     *                                          callback function.
     * @return  bool                            Returns TRUE on success or FALSE on failure.
     */
    function walk(&$arg, $cb, $userdata = null)
    /**/
    {
        return array_walk($arg, $cb, $userdata);
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
    class collection extends \ArrayIterator
    /**/
    {
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
         * @octdoc  v:collection/$count
         * @var     int
         */
        protected $count = 0;
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
            } elseif ($value instanceof \ArrayObject || $value instanceof \ArrayIterator) {
                // an ArrayObject or ArrayIterator will be casted to a PHP array first
                $value = (array)$value;
            } elseif (!is_array($value)) {
                // not an array
                throw new \Exception('don\'t know how to handle parameter of type "' . gettype($value) . '"');
            }
        
            $this->count = count($value);

            parent::__construct($value);
        }

        /** Iterator **/
        
        /**
         * Move forward to next element.
         *
         * @octdoc  m:collection/next
         */
        public function next()
        /**/
        {
            ++$this->position;
            parent::next();
        }
        
        /**
         * Move backwards to previous element.
         *
         * @octdoc  m:collection/prev
         */
        public function prev()
        /**/
        {
            --$this->position;
            parent::seek($this->position);
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
            parent::rewind();
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
            parent::seek($position);
        }
    
        /** ArrayAccess **/
    
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
            if (is_null($offs) || !$this->offsetExists($offs)) {
                ++$this->count;
            }
            
            parent::offsetSet($offs, $value);
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
            if ($this->offsetExists($offs)) {
                --$this->count;
            }
            
            parent::offsetUnset($offs);
        }

        /** Serializable **/

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

        /** Special collection functionality **/

        /**
         * Return current position of iterator.
         *
         * @octdoc  m:collection/getPosition
         * @return  int                     Iterator position.   
         */
        public function getPosition()
        /**/
        {
            return $this->position;
        }

        /**
         * Exchange the array for another one.
         *
         * @octdoc  m:collection/exchangeArray
         * @param   mixed       $value      The new array or object to exchange to current data with.
         * @return  array                   Data stored in collection
         */
        public function exchangeArray($value)
        /**/
        {
            $return = $this->getArrayCopy();
            
            $this->__construct($value);
            
            return $return;
        }

        /**
         * Rename keys of collection but preserve the ordering of the collection.
         *
         * @octdoc  m:collection/keyrename
         * @param   array       $map        Map of origin name to new name.
         */
        public function keyrename($map)
        /**/
        {
            $data = $this->getArrayCopy();
            $data = array_combine(array_map(function($v) use ($map) {
                return (isset($map[$v])
                        ? $map[$v]
                        : $v);
            }, array_keys($data)), array_values($data));
            
            parent::exchangeArray($data);
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
            $data = $this->getArrayCopy();
            
            if (is_object($value) && ($value instanceof ArrayObject || $value instanceof ArrayIterator)) {
                $value = (array)$value;
            } elseif (!is_array($value)) {
                throw new Exception('don\'t know how to handle parameter of type "' . gettype($array) . '"');
            }

            $data = array_merge($value, $data);
            
            parent::exchangeArray($data);
        }
    }
}

