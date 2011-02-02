<?php

namespace org\octris\core\type {
    /**
     * Collection type. Implements special access on array objects.
     *
     * @octdoc      c:type/collection
     * @copyright   copyright (c) 2010-2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
 class collection implements \IteratorAggregate, \ArrayAccess, \Serializable, \Countable
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
         * Constructor.
         *
         * @octdoc  m:collection/__construct
         * @param   mixed       $value      Optional value to initialize collection with.
         */
        public function __construct($value = null)
        /**/
        {
            if (is_null($value)) {
                $value = array();
            } elseif (is_scalar($value)) {
                // a scalar will be splitted into bytes
                $value = str_split((string)$value, 1);
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
    
        /**
         * Returns iterator object for collection.
         *
         * @octdoc  m:collection/getIterator
         * @return  \ArrayIterator                  Instance of iterator.
         */
        public function getIterator()
        /**/
        {
            return new \ArrayIterator($this->data);
        }
        
        /**
         * Returns copy of collection data as PHP array.
         *
         * @octdoc  m:collection/getArrayCopy
         * @return  array                           Collection data.
         */
        public function getArrayCopy()
        /**/
        {
            return $this->data;
        }

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
         * UTF-8 encode collection values.
         *
         * @octdoc  m:collection/utf8Encode
         * @return  \org\octris\core\type\collection    Encoded data.
         */
        public function utf8Encode()
        /**/
        {
            $tmp = $this->data;
            
            array_walk_recursive($tmp, function(&$v) {
                if (is_string($v) && !preg_match('%^(?:  
                [\x09\x0A\x0D\x20-\x7E] # ASCII  
                | [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte  
                | \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs  
                | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte  
                | \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates  
                | \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3  
                | [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15  
                | \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16  
                )*$%xs', $v)) {
                    $v = utf8_encode($v);
                }            
            });

            return new collection($tmp);
        }

        /**
         * UTF-8 decode collection values.
         *
         * @octdoc  m:collection/utf8Decode
         * @return  \org\octris\core\type\collection    Decoded data.
         */
        public function utf8Decode()
        /**/
        {
            $tmp = $this->data;
            
            array_walk_recursive($tmp, function(&$v) {
                if (is_string($v) && preg_match('%^(?:  
                [\x09\x0A\x0D\x20-\x7E] # ASCII  
                | [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte  
                | \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs  
                | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte  
                | \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates  
                | \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3  
                | [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15  
                | \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16  
                )*$%xs', $v)) {
                    $v = utf8_decode($v);
                }            
            });

            return new collection($tmp);
        }
    }
}

