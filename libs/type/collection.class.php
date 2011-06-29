<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\type {
    /**
     * Collection type. Implements special access on array objects.
     *
     * @octdoc      c:type/collection
     * @copyright   copyright (c) 2010-2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class collection extends \ArrayObject
    /**/
    {
        /**
         * Stores keys of items stored in collection.
         *
         * @octdoc  v:collection/$keys
         * @var     array
         */
        protected $keys = array();
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
            if (($tmp = static::normalize($value)) === false) {
                // not an array
                throw new \Exception('don\'t know how to handle parameter of type "' . gettype($tmp) . '"');
            }
        
            $this->keys = array_keys($tmp);
        
            parent::__construct($tmp);
        }

        /**
         * Return iterator for collection.
         *
         * @octdoc  m:collection/getIterator
         * @return  \org\octris\core\type\iterator          Iterator instance for iterating over collection.
         */
        public function getIterator()
        /**/
        {
            return new \org\octris\core\type\iterator($this);
        }
        
        /**
         * Return class name of 
         *
         * @octdoc  m:collection/getIteratorClass
         * @return  string                                  Name if iterator class currently set.
         */
        public function getIteratorClass()
        /**/
        {
            return '\org\octris\core\type\iterator';
        }

        /**
         * Overwrite method to prevent changing iterator class, because it's currently not support.
         *
         * @octdoc  m:collection/setIteratorClass
         * @param   string      $class                      Name of iterator class to set for collection.
         */
        public function setIteratorClass($class)
        /**/
        {
            throw new \Exception(__METHOD__ . ' is not currently supported');
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
            if (is_null($offs)) {
                // $...[] =
                $inc = (int)in_array(0, $this->keys);               // if 0 is already in, we have to increment next index
                $idx = max(array_merge(array(0), $this->keys));     // get next highest numeric index
                $this->keys[] = $idx + $inc;
            } elseif (!parent::offsetExists($offs)) {
                // new offset
                $this->keys[] = $offs;
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
            if (($idx = array_search($offs, $this->keys)) !== false) {
                unset($offs[$idx]);
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
            $tmp = unserialize($data);
            
            $this->__construct(unserialize($data));
        }

        /** Special collection functionality **/

        /**
         * Returns value for item stored in the collection at the specified position.
         *
         * @octdoc  m:collection/getValue
         * @param   int         $position       Position to return value of item for.
         * @return  mixed                       Value stored at the specified position.
         */
        public function getValue($position)
        /**/
        {
            return $this->offsetGet($this->keys[$position]);
        }
        
        /**
         * Returns item key for specified position in the collection.
         *
         * @octdoc  m:collection/getKey
         * @param   int         $position       Position to return key of item for.
         * @return  mixed                       Key of the item at specified position.
         */
        public function getKey($position)
        /**/
        {
            return $this->keys[$position];
        }
        
        /**
         * Checks if the specified position points to an element in the collection.
         *
         * @octdoc  m:collection/isValid
         * @param   int         $position       Position to check.
         * @return  true                        Returns tue if an element exists at specified position. Returns false in case of an error.
         */
        public function isValid($position)
        /**/
        {
            return array_key_exists($position, $this->keys);
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
            if (($tmp = static::normalize($value)) === false) {
                // not an array
                throw new \Exception('don\'t know how to handle parameter of type "' . gettype($tmp) . '"');
            } else {
                $this->keys = array_keys($tmp);
            
                $return = $this->getArrayCopy();
            
                parent::exchangeArray($tmp);
            }
            
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
            
            $this->exchangeArray($data);
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
            
            $this->exchangeArray($data);
        }
        
        /** Static functions to work with arrays and collections **/
        
        /**
         * This method converts the input type to an array. The method can handle the following types:
         *
         *  * null -- an empty array is returned
         *  * scalar -- will be splitted by it's characters (UTF-8 safe)
         *  * array -- is returned as array
         *  * ArrayObject, ArrayIterator, \org\octris\core\type\collection, \org\octris\core\type\iterator -- get converted to an array
         *
         * for all other types 'false' is returned.
         *
         * @octdoc  m:collection/normalize
         * @param   mixed       $value          Value to normalize
         * @return  array|bool                  Returns an array if normalization succeeded. In case of an error 'false' is returned.
         */
        public static function normalize($value)
        /**/
        {
            if (is_null($value)) {
                // initialize empty array if no value is specified
                $return = array();
            } elseif (is_scalar($value)) {
                // a scalar will be splitted into it's character, UTF-8 safe.
                $return = \org\octris\core\type\string\str_split((string)$value, 1);
            } elseif ($value instanceof \ArrayObject || $value instanceof \ArrayIterator || $value instanceof \org\octris\core\type\iterator) {
                // an ArrayObject or ArrayIterator will be casted to a PHP array first
                $return = $value->getArrayCopy();
            } elseif (is_array($value)) {
                $return = $value;
            } else {
                $return = false;
            }
            
            return $return;
        }
    }
}

