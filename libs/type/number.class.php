<?php

namespace org\octris\core\type {
    class number extends \org\octris\core\type {
        /**
         * Value of object.
         *
         * @octdoc  v:number/$value
         * @var     float
         */
        protected $value = 0;
        /**/
        
        /**
         * Constructor.
         *
         * @octdoc  m:number/__construct
         * @param   float       $value      Optional value for number.
         */
        public function __construct($value = 0)
        /**/
        {
            $this->value = $value;
        }

        /**
         * Method is called, when number object is casted to a string.
         *
         * @octdoc  m:number/__toString
         * @return  string                      Value of object.
         */
        public function __toString()
        /**/
        {
            return (string)$this->value;
        }

        /**
         * Magic caller to implement calculation functionality.
         *
         * @octdoc  m:number/__call
         * @param   string              $func               Name of function to perform.
         * @param   array               $args               Arbitrary number of arguments of type float or money.
         */
        public function __call($func, $args)
        /**/
        {
            $args = array_map(function($v) {
                if ($v instanceof number) {
                    $v = (float)$v->get();
                } else {
                    $v = (float)$v;
                }
                
                return $v;
            });
            
            switch ($func) {
            case 'add':
                $this->value += array_sum($args);
                break;
            case 'sub':
                $this->value = array_reduce($args, function($v, $w) {
                    return $v -= $w;
                }, $this->value);
                break;
            case 'mul':
                $this->value *= array_product($args);
                break;
            case 'div':
                $this->value = array_reduce(
                    array_filter($args, function($v) {
                        return ((int)$v !== 0);
                    }, 
                    function($v, $w) {
                        return $v /= $w;
                    }, 
                    $this->value
                );
                break;
            case 'mod':
                $this->value = array_reduce($args, function($v, $w) {
                    return $v %= $w;
                }, $this->value);
                break;
            }
        }
        
        /**
         * Return value of object.
         *
         * @octdoc  m:number/get
         * @return  float                                   Value.
         */
        public function get()
        /**/
        {
            return $this->value;
        }

        /**
         * Set value of object.
         *
         * @octdoc  m:number/set
         * @param   float               $amount             Value to set.
         */
        public function set($value)
        /**/
        {
            $this->value = $value;
        }
    }
}
