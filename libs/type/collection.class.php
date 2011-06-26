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
            } elseif (!($value = collection\normalize($value))) {
                // not an array nor a collection
                throw new Exception('don\'t know how to handle parameter of type "' . gettype($array) . '"');
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

