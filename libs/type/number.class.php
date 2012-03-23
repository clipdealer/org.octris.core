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
     * Number type.
     *
     * @octdoc      c:type/number
     * @copyright   copyright (c) 2010-2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class number extends \org\octris\core\type 
    /**/
    {
        /**
         * Value of object.
         *
         * @octdoc  p:number/$value
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
         * @param   string              $func                                       Name of function to perform.
         * @param   array               $args                                       Arbitrary number of arguments of type float or money.
         * @return  \org\octris\core\type\number|\org\octris\core\type\money        Instance of current object.
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
                    }), 
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

            return $this;
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
