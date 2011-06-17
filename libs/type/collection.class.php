<?php

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
            } elseif (is_object($value)) {
                if (($value instanceof collection) || ($value instanceof collection\Iterator) || ($value instanceof \ArrayIterator)) {
                    $value = $value->getArrayCopy();
                } else {
                    $value = (array)$value;
                }
            } elseif (!is_array($value)) {
                throw new Exception('don\'t know how to handle parameter of type "' . gettype($array) . '"');
            }
        
            $this->keys = array_keys($value);
            $this->data = $value;
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
            return ($this->position < count($this->data));
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
            return count($this->data);
        }

        /** Special collection functionality **/

        /**
         * Rename keys of collection but preserve the ordering of the collection.
         *
         * @octdoc  m:collection/keyrename
         * @param   array               $map                Map of origin name to new name.
         * @return  \org\octris\core\type\collection        New collection with renamed keys.
         */
        public function keyrename($map)
        /**/
        {
            return new collection(array_combine(array_map(function($v) use ($map) {
                return (isset($map[$v])
                        ? $map[$v]
                        : $v);
            }, array_keys($this->data)), array_values($this->data)));
        }

        /**
         * Flatten a collection. Convert a (nested) collection into a flat collection with expanded keys
         *
         * @octdoc  m:collection/flatten
         * @param   string      $sep                    Optional separator for expanding keys.
         * @return  \org\octris\core\type\collection    Flattened collection.
         */
        public function flatten($sep = '.')
        /**/
        {
            $tmp = array();

            $array = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($this->data), \RecursiveIteratorIterator::SELF_FIRST);
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

            return new collection($tmp);
        }

        /**
         * Deflatten a flat collection.
         *
         * @octdoc  m:collection/deflatten
         * @return  \org\octris\core\type\collection    Deflattened collection.
         */
        public function deflatten()
        /**/
        {
            $tmp = array();

            foreach ($this->data as $k => $v) {
                $key  = explode('.', $k);
                $ref =& $tmp;

                foreach ($key as $part) {
                    if (!isset($ref[$part])) {
                        $ref[$part] = array();
                    }

                    $ref =& $ref[$part];
                }

                $ref = $v;
            }

            return new collection($tmp);
        }
        
        /**
         * Sets defaults for collection. Values are only set, if the keys of the values are not already available in collection.
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
        
        /**
         * Merge current collection with one or multiple others.
         *
         * @octdoc  m:collection/merge
         * @param   mixed       $arg1, ...      Array(s) / collection(s) to merge.
         */
        public function merge()
        /**/
        {
            for ($i = 0, $cnt = func_num_args(); $i < $cnt; ++$i) {
                $arg = func_get_arg($i);
                
                if (is_array($arg)) {
                    $this->data = array_merge($this->data, $arg);
                } elseif (is_object($arg)) {
                    if (($arg instanceof collection) || ($arg instanceof collection\Iterator) || ($arg instanceof \ArrayIterator)) {
                        $arg = $arg->getArrayCopy();
                    } else {
                        $arg = (array)$arg;
                    }

                    $this->data = array_merge($this->data, $arg);
                }
            }
        }
            
        /**
         * Return keys of collection.
         *
         * @octdoc  m:collection/getKeys
         * @return  array                               Array of stored keys.
         */
        public function getKeys()
        /**/
        {
            return $this->keys;
        }
    }
}

