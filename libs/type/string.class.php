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

/** procedural access to multibyte-safe string functions **/
namespace org\octris\core\type\string {
    /**
     * Return length of the given string.
     *
     * @octdoc  m:string/strlen
     * @param   string      $string         String to return length for.
     * @param   string      $encoding       Optional encoding to use as default for all string operations.
     */
    function strlen($str, $encoding = 'UTF-8')
    /**/
    {
        return mb_strlen($str, $encoding);
    }
    
    /**
     * Returns the numeric position of the first occurrence of needle in the haystack string. 
     * This function can take a full string as the needle parameter and the entire string will be used.
     *
     * @octdoc  m:string/strpos
     * @param   string      $string         String to return length for.
     * @param   string      $needle         The position counted from the beginning of haystack.
     * @param   int         $offset         The search offset. If it is not specified, 0 is used.
     * @param   string      $encoding       Optional encoding to use as default for all string operations.
     * @return  int|bool                    Returns the numeric position of the first occurrence of needle in the 
     *                                      haystack string. If needle is not found, it returns FALSE.
     */
    function strpos($string, $needle, $offset = 0, $encoding = 'UTF-8')
    /**/
    {
        return mb_strpos($string, $needle, $offset, $encoding);
    }
    
    /**
     * Performs a multibyte safe strrpos() operation based on the number of characters. 
     * Needle position is counted from the beginning of haystack. First character's position is 0. 
     * Second character position is 1.
     *
     * @octdoc  m:string/strrpos
     * @param   string      $string         String to return length for.
     * @param   string      $needle         The string to find in haystack.
     * @param   int         $offset         May be specified to begin searching an arbitrary number of characters 
     *                                      into the string. Negative values will stop searching at an arbitrary point
     *                                      prior to the end of the string.
     * @param   string      $encoding       Optional encoding to use as default for all string operations.
     * @return  int|bool                    Returns the numeric position of the last occurrence of needle in the 
     *                                      haystack string. If needle is not found, it returns FALSE.
     */
    public function strrpos($string, $needle, $offset = null, $encoding = 'UTF-8')
    /**/
    {
        return mb_strrpos($string, $needle, $offset, $encoding);
    }
    
    /**
     * Convert a specified string to 7bit.
     *
     * @octdoc  f:string/to7bit
     * @param   string      $string         String to convert
     * @param   string      $encoding       Optional encoding to use.
     */
    function to7bit($string, $encoding = 'UTF-8')
    /**/
    {
        $string = mb_convert_encoding($string, 'HTML-ENTITIES', $encoding);
        $string = preg_replace(
            array('/&szlig;/', '/&(..)lig;/', '/&([aouAOU])uml;/','/&(.)[^;]*;/'),
            array('ss', '$1', '$1'.'e', '$1'),
            $string
        );
        
        return $string;
    }
}

/** object-oriented access to multibyte-safe string functions **/
namespace org\octris\core\type {
    use \org\octris\core\type\string as string;
    
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
         * Default encoding for string operations.
         *
         * @octdoc  v:string/$encoding
         * @var     string
         */
        protected $encoding = 'UTF-8';
        /**/
        
        /**
         * Constructor.
         *
         * @octdoc  m:string/__construct
         * @param   string      $string         String to initialize new instance with.
         * @param   string      $encoding       Optional encoding to use as default for all string operations.
         */
        public function __construct($string, $encoding = 'UTF-8')
        /**/
        {
            $this->string   = $string;
            $this->encoding = $encoding;
        }
        
        /**
         * Return string representation of object if it's casted to a string.
         *
         * @octdoc  m:string/__toString
         * @return  string                      string representation of object
         */
        public function __toString()
        /**/
        {
            return $this->string;
        }
        
        /**
         * Return length of the given string.
         *
         * @octdoc  m:string/strlen
         * @return  int                         Length of string.
         */
        public function strlen()
        /**/
        {
            return string\strlen($this->string, $this->encoding);
        }
        
        /**
         * Returns the numeric position of the first occurrence of needle in the haystack string. 
         * This function can take a full string as the needle parameter and the entire string will be used.
         *
         * @octdoc  m:string/strpos
         * @param   string      $needle         The position counted from the beginning of haystack.
         * @param   int         $offset         The search offset. If it is not specified, 0 is used.
         * @return  int|bool                    Returns the numeric position of the first occurrence of needle in the 
         *                                      haystack string. If needle is not found, it returns FALSE.
         */
        public function strpos($needle, $offset = 0)
        /**/
        {
            return string\strpos($this->string, $needle, $offset, $this->encoding);
        }
        
        /**
         * Performs a multibyte safe strrpos() operation based on the number of characters. 
         * Needle position is counted from the beginning of haystack. First character's position is 0. 
         * Second character position is 1.
         *
         * @octdoc  m:string/strrpos
         * @param   string      $needle         The string to find in haystack.
         * @param   int         $offset         May be specified to begin searching an arbitrary number of characters 
         *                                      into the string. Negative values will stop searching at an arbitrary point
         *                                      prior to the end of the string.
         * @return  int|bool                    Returns the numeric position of the last occurrence of needle in the 
         *                                      haystack string. If needle is not found, it returns FALSE.
         */
        public function strrpos($needle, $offset = null)
        /**/
        {
            return string\strrpos($this->string, $needle, $offset, $this->encoding);
        }
        
        /**
         * Convert a string to 7bit
         *
         * @octdoc  m:string/to7bit
         * @param   
         */
        public function to7bit()
        /**/
        {
            string\to7bit($this->string, $this->encoding);
        }
        
        /**
         * Concat string object with an arbitrary amount of other strings or string objects.
         *
         * @octdoc  m:string/concat
         * @param   string      ...             One or multiple strings to concatenate.
         */
        public function concat()
        /**/
        {
            $
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
