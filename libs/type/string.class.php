<?php

namespace org\octris\core\type {
    /****c* type/string
     * NAME
     *      string
     * FUNCTION
     *      string type class
     * COPYRIGHT
     *      copyright (c) 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */

    class string {
        static $map = array(
            'substr'  => 'substr',
            'repeat'  => 'str_repeat',
            'replace' => 'str_replace'
        );
    
        function __construct($str) {
            $this->str = $str;
        }
    
        function __toString() {
            return $this->str;
        }
    
        // user defined function, or wrapper, when args are not in correct order
        function x() {
            return new static($this->str . 'x');
        }
    
        function gsub($pattern, $replace) {
            return new static(preg_replace($pattern, $replace, $this->str));
        }
    
        function __call($name, $args) {
            array_unshift($args, $this->str);
        
            if (!isset(static::$map[$name])) {
                throw Exception('method "$name" not allowed!');
            }
        
            return new static(call_user_func_array(static::$map[$name], $args));
        }
    }
}

