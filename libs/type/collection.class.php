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

    class collection implements \Iterator, \ArrayAccess, \Serializable, \Countable {
        function __construct($array) {
            if (is_scalar($array)) {
                $array = str_split((string)$array, 1);
            } elseif (is_object($array)) {
                if ($array instanceof octris_type_array) {
                    $array = $array->export();
                } else {
                    $array = (array)$array;
                }
            }
        
            $this->keys   = array_keys($array);
            $this->values = array_values($array);
        
            $this->position = 0;
            $this->count    = count($array);
        }
    
        protected function getItem($pos) {
            $item = $this->values[$pos];
        
            switch (gettype($item)) {
            case 'array':
                $item = new self($item);
                break;
            case 'object':
                $item = new self((array)$item);
                break;
            }
        
            return (object)array(
                'item'      => $item,
                'key'       => $this->keys[$pos],
                'pos'       => $pos,
                'count'     => $this->count,
                'is_first'  => ($pos == 0),
                'is_last'   => ($pos == $this->count - 1)
            );
        }
    
        function export() {
            return array_combine($this->keys, $this->values);
        }
    
        function current() {
            return $this->getItem($this->position);
        }
    
        function rewind() {
            $this->position = 0;
        }
    
        function next() {
            ++$this->position;
        }
    
        function key() {
            return $this->keys[$this->position];
        }
    
        function valid() {
            return isset($this->values[$this->position]);
        }
    
        function offsetExists($offs) {
            return (in_array($offs, $this->keys));
        }
    
        function offsetGet($offs) {
            $idx = array_search($offs, $this->keys, true);
        
            return ($idx !== false ? $this->getItem($idx) : false);
        }
    
        function offsetSet($offs, $value) {
            // is_null implements $...[] = ...
            if (!is_null($offs) && ($idx = array_search($offs, $this->keys, true)) !== false) {
                $this->values[$idx] = $value;
            } else {
                $this->keys[]   = $offs;
                $this->values[] = $value;
            }
        }

        function offsetUnset($offs) {
            $idx = array_search($offs, $this->keys, true);

            if ($idx !== false) {
                $tmp = array_combine($this->keys, $this->values);
                unset($tmp[$offs]);
            
                $this->keys   = array_keys($tmp);
                $this->values = array_values($tmp);
            
                $this->count  = count($this->keys);
            }
        }
    
        function serialize() {
            return serialize(array_combine($this->keys, $this->values));
        }
    
        function unserialize($data) {
            $this->__construct($data);
        }
    
        function count() {
            return $this->count;
        }
    }
}

