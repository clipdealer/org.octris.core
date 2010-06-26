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

    final class wrapper implements \IteratorAggregate, \ArrayAccess, \Serializable, \Countable {
        /****v* wrapper/$cache
         * SYNOPSIS
         */
        protected $cache = array();
        /*
         * FUNCTION
         *      cache for parameters
         ****
         */

        /****v* wrapper/$keys
         * SYNOPSIS
         */
        protected $keys = array();
        /*
         * FUNCTION
         *      parameter names
         ****
         */

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
                $this->keys  = array_keys($source);
                $this->cache = array();
                
                foreach ($source as $k => $v) {
                    $this->cache[$k] = (object)$this->defaults;
                    $this->cache[$k]->isSet       = true;
                    $this->cache[$k]->unsanitized = $v;
                }
            } else {
                $this->cache = array();
            }
        }

        function getIterator() {
            return new \ArrayIterator($this->cache);
        }
    
        function offsetExists($offs) {
            return (in_array($offs, $this->keys));
        }
    
        function offsetGet($offs) {
            $idx = array_search($offs, $this->keys, true);
        
            return ($idx !== false ? $this->cache[$this->keys[$idx]] : false);
        }
    
        function offsetSet($offs, $value) {
            $tmp = (object)$this->defaults;
            $tmp->isSet       = true;
            $tmp->isValid     = true;
            $tmp->isValidated = true;
            $tmp->unsanitized = $value;
            $tmp->value       = $value;
            
            // is_null implements $...[] = ...
            if (!is_null($offs) && ($idx = array_search($offs, $this->keys, true)) !== false) {
                $this->values[$this->keys[$idx]] = $tmp;
            } else {
                $this->keys[]        = $offs;
                $this->values[$offs] = $tmp;
            }
        }

        function offsetUnset($offs) {
            $idx = array_search($offs, $this->keys, true);

            if ($idx !== false) {
                unset($this->keys[$idx]);
                unset($this->cache[$offs]);
            }
        }
    
        function serialize() {
            return serialize($this->cache);
        }
    
        function unserialize($data) {
            $this->__construct($data);
        }
    
        function count() {
            return count($this->cache);
        }
    }

    // enable validation for superglobals
    $_COOKIE  = new wrapper($_COOKIE);
    $_GET     = new wrapper($_GET);
    $_POST    = new wrapper($_POST);
    $_SERVER  = new wrapper($_SERVER);
    $_ENV     = new wrapper($_ENV);
    $_REQUEST = new wrapper($_REQUEST);
}

?>