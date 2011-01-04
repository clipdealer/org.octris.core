<?php

/*
 * Copyright (c) 2011, Harald Lapp <harald.lapp@gmail.com>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Harald Lapp nor the names of its
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 */

namespace org\octris\core\type {
    /**
     * Multibyte safe string class.
     * 
     * @octdoc      c:type/string
     * @copyright   copyright (c) 2011 by Harald Lapp, Documentation taken from the official PHP Documentation
     * @author      Harald Lapp <harald.lapp@gmail.com>
     */
    class string
    /**/
    {
        /**
         * Stored string.
         *
         * @octdoc  v:string/$string
         * @var     string
         */
        protected $string = '';
        /**/
        
        /**
         * Constructor.
         *
         * @octdoc  m:string/__construct
         * @param   string      $string         String to initialize new instance with.
         */
        public function __construct($string)
        /**/
        {
            $this->string = $string;
        }
        
        /**
         * Return string representation of object if it's casted to a string.
         *
         * @octdoc  m:string/__tostring
         * @return  string                      string representation of object
         */
        public function __tostring()
        /**/
        {
            return $this->string;
        }
        
        /**
         * Magic caller for internal static methods.
         *
         * @octdoc  m:string/__call
         * @param   string      $name           Name of function to call.
         * @param   mixed       $arg, ...       Optional arguments.
         * @return  mixed                       Return value according to the called function.
         */
        public function __call($name, array $args = array())
        /**/
        {
            array_unshift($args, $this->string);
            
            return call_user_func_array(array(__NAMESPACE__ . '\string', $name), $args);
        }
        
        /**
         * Magic caller for internal static methods.
         *
         * @octdoc  m:string/__callStatic
         * @param   string      $name           Name of function to call.
         * @param   string      $string         String to process.
         * @param   mixed       $arg, ...       Optional arguments.
         * @return  mixed                       Return value according to the called function.
         */
        public static function __callStatic($name, $args)
        /**/
        {
            return call_user_func_array(array(__NAMESPACE__ . '\string', $name), $args);
        }
        
        /**
         * Return length of the given string.
         *
         * @octdoc  m:string/length
         * @param   string      $string         String to return length for.
         */
        protected static function length($string)
        /**/
        {
            return mb_strlen($string);
        }
        
        /**
         * Returns the numeric position of the first occurrence of needle in the haystack string. 
         * This function can take a full string as the needle parameter and the entire string will be used.
         *
         * @octdoc  m:string/pos
         * @param   string      $haystack       The string being checked.
         * @param   string      $needle         The position counted from the beginning of haystack.
         * @param   int         $offset         The search offset. If it is not specified, 0 is used.
         * @return  int|bool                    Returns the numeric position of the first occurrence of needle in the 
         *                                      haystack string. If needle is not found, it returns FALSE.
         */
        protected static function pos($haystack, $needle, $offset = 0)
        /**/
        {
            return mb_strpos($haystack, $needle, $offset);
        }
        
        /**
         * Performs a multibyte safe strrpos() operation based on the number of characters. 
         * Needle position is counted from the beginning of haystack. First character's position is 0. 
         * Second character position is 1.
         *
         * @octdoc  m:string/rpos
         * @param   string      $haystack       The string being checked, for the last occurrence of needle.
         * @param   string      $needle         The string to find in haystack.
         * @param   int         $offset         May be specified to begin searching an arbitrary number of characters 
         *                                      into the string. Negative values will stop searching at an arbitrary point
         *                                      prior to the end of the string.
         * @return  int|bool                    Returns the numeric position of the last occurrence of needle in the 
         *                                      haystack string. If needle is not found, it returns FALSE.
         */
        protected static function rpos($haystack, $needle, $offset = null)
        /**/
        {
            return mb_strrpos($haystack, $needle, $offset);
        }
        
        /*

        TODO:
        
        substr()	-> mb_substr()
        strtolower()	-> mb_strtolower()
        strtoupper()	-> mb_strtoupper()
        substr_count()	-> mb_substr_count()
        ereg()		-> mb_ereg()
        eregi()		-> mb_eregi()
        ereg_replace()	-> mb_ereg_replace()
        eregi_replace()	-> mb_eregi_replace()	
        split()		-> mb_split()
        
        */
    }
}
