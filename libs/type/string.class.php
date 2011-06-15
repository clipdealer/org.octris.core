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

/** procedural access to UTF-8 string functions **/
namespace org\octris\core\type\string {
    /**
     * Regular expression match for multibyte string.
     *
     * @octdoc  f:string/match
     * @param   string      $pattern        The search pattern.
     * @param   string      $string         The search string.
     * @param   string      $options        If 'i' is specified for this parameter, the case will be ignored.
     * @return  array|bool                  The function returns substring of matched string. If no matches 
     *                                      are found or an error happens, FALSE will be returned.     
     */
    function match($pattern, $string, $options = '')
    /**/
    {
        $m = array();
        
        mb_regex_encoding('UTF-8');
        
        if (strpos($options, 'i') !== false) {
            $return = mb_eregi($pattern, $string, $m);
        } else {
            $return = mb_ereg($pattern, $string, $m);
        }
        
        return ($return === false ? false : $m);
    }

    /**
     * Replace regular expression with multibyte support.
     *
     * @octdoc  f:string/replace
     * @param   string      $pattern        The search pattern.
     * @param   string      $string         The search string.
     * @param   string      $options        Matching condition can be set by option parameter. If i is specified for this
     *                                      parameter, the case will be ignored. If x is specified, white space will be
     *                                      ignored. If m is specified, match will be executed in multiline mode and line
     *                                      break will be included in '.'. If p is specified, match will be executed in 
     *                                      POSIX mode, line break will be considered as normal character. If e is 
     *                                      specified, replacement string will be evaluated as PHP expression.
     * @return  string                      The resultant string on success, or FALSE on error.
     */
    function replace($pattern, $replacement, $string, $options = 'msr')
    /**/
    {
        mb_regex_encoding('UTF-8');
        
        return mb_ereg_replace($pattern, $replacement, $string, $options);
    }
    
    /**
     * Split multibyte string using regular expression.
     *
     * @octdoc  f:string/split
     * @param   string      $pattern        The regular expression pattern.
     * @param   string      $string         The string being split.
     * @return  array                       Array of splitted strings.
     */
    function split($pattern, $string)
    /**/
    {
        mb_regex_encoding('UTF-8');
        
        return mb_split($pattern, $string);
    }
    
    /**
     * Finds position of first occurrence of a string within another, case insensitive.
     *
     * @octdoc  f:string/stripos
     * @param   string      $string         String to return length for.
     * @param   string      $needle         The position counted from the beginning of haystack.
     * @param   int         $offset         The search offset. If it is not specified, 0 is used.
     * @return  int|bool                    Returns the numeric position of the first occurrence of needle in the 
     *                                      haystack string. If needle is not found, it returns FALSE.
     */
    function stripos($string, $needle, $offset = 0)
    /**/
    {
        return mb_stripos($string, $needle, $offset, 'UTF-8');
    }

    /**
     * Finds first occurrence of a string within another, case insensitive.
     *
     * @octdoc  f:string/stristr
     * @param   string      $string         The string from which to get the first occurrence of needle.
     * @param   string      $needle         The string to find in string.
     * @param   bool        $part           Determines which portion of haystack this function returns. If set to 
     *                                      TRUE, it returns all of string from the beginning to the first occurrence 
     *                                      of needle. If set to FALSE, it returns all of string from the first
     *                                      occurrence of needle to the end.
     * @return  string|bool                 Returns the portion of string, or FALSE if needle is not found.
     */
    function stristr($string, $needle, $part = false)
    /**/
    {
        return mb_stristr($string, $needle, $part, 'UTF-8');
    }
    
    /**
     * Return length of the given string.
     *
     * @octdoc  f:string/strlen
     * @param   string      $string         String to return length for.
     */
    function strlen($string)
    /**/
    {
        return mb_strlen($string, 'UTF-8');
    }
    
    /**
     * Pad a string to a certain length with another string.
     *
     * @octdoc  f:string/strpad
     * @param   string      $string         String to pad.
     * @param   int         $length         Length to pad string to.
     * @param   string      $chr            Optional character to use for padding.
     * @param   string      $type           Optional argument can be STR_PAD_RIGHT, STR_PAD_LEFT, or STR_PAD_BOTH.
     * @return  string                      Padded string.
     */
    function strpad($string, $length, $chr = ' ', $type = STR_PAD_RIGHT)
    /**/
    {
        if (!in_array($type, array(STR_PAD_LEFT, STR_PAD_RIGHT, STR_PAD_BOTH))) {
            $type = STR_PAD_RIGHT;
        }
        
        $diff = strlen($string) - mb_strlen($string, 'UTF-8');

        return str_pad($string, $length + $diff, $chr, $type);
    }

    /**
     * Find position of first occurrence of string in a string.
     *
     * @octdoc  f:string/strpos
     * @param   string      $string         String to return length for.
     * @param   string      $needle         The position counted from the beginning of haystack.
     * @param   int         $offset         The search offset. If it is not specified, 0 is used.
     * @return  int|bool                    Returns the numeric position of the first occurrence of needle in the 
     *                                      haystack string. If needle is not found, it returns FALSE.
     */
    function strpos($string, $needle, $offset = 0)
    /**/
    {
        return mb_strpos($string, $needle, $offset, 'UTF-8');
    }
    
    /**
     * Replace all occurrences of the search string with the replacement string.
     *
     * @octdoc  f:string/strreplace
     * @param   string      $search         The value being searched for, otherwise known as the needle. An array may be used to designate multiple needles.
     * @param   string      $replace        The replacement value that replaces found search values. An array may be used to designate multiple replacements.
     * @param   string      $subject        The string or array being searched and replaced on, otherwise known as the haystack. If subject is an array, 
     *                                      then the search and replace is performed with every entry of subject, and the return value is an array as well.
     * @param   int         $count          If passed, this will be set to the number of replacements performed.
     * @return  string                      This function returns a string or an array with the replaced values.
     */
    function strreplace($search, $replace, $subject, &$count = null)
    /**/
    {
        return str_replace($search, $replace, $subject, $count);
    }
    
    /**
     * Reverse a string.
     *
     * @octdoc  f:string/strrev
     * @param   string      $string         The string to be reversed.
     * @return  string                      Reversed string.
     */
    function strrev($string)
    /**/
    {
        return implode('', array_reverse(preg_split('//us', $string)));
    }
    
    /**
     * Find position of last occurrence of a string in a string.
     *
     * @octdoc  f:string/strrpos
     * @param   string      $string         String to return length for.
     * @param   string      $needle         The string to find in haystack.
     * @param   int         $offset         May be specified to begin searching an arbitrary number of characters 
     *                                      into the string. Negative values will stop searching at an arbitrary point
     *                                      prior to the end of the string.
     * @return  int|bool                    Returns the numeric position of the last occurrence of needle in the 
     *                                      haystack string. If needle is not found, it returns FALSE.
     */
    function strrpos($string, $needle, $offset = null)
    /**/
    {
        return mb_strrpos($string, $needle, $offset, 'UTF-8');
    }

    /**
     * Finds position of last occurrence of a string within another, case insensitive.
     *
     * @octdoc  f:string/strripos
     * @param   string      $string         String to return length for.
     * @param   string      $needle         The string to find in haystack.
     * @param   int         $offset         May be specified to begin searching an arbitrary number of characters 
     *                                      into the string. Negative values will stop searching at an arbitrary point
     *                                      prior to the end of the string.
     * @return  int|bool                    Returns the numeric position of the last occurrence of needle in the 
     *                                      haystack string. If needle is not found, it returns FALSE.
     */
    function strripos($string, $needle, $offset = null)
    /**/
    {
        return mb_strripos($string, $needle, $offset, 'UTF-8');
    }

    /**
     * Finds first occurrence of a string within another.
     *
     * @octdoc  f:string/strstr
     * @param   string      $string         The string from which to get the first occurrence of needle.
     * @param   string      $needle         The string to find in string.
     * @param   bool        $part           Determines which portion of haystack this function returns. If set to 
     *                                      TRUE, it returns all of string from the beginning to the first occurrence 
     *                                      of needle. If set to FALSE, it returns all of string from the first
     *                                      occurrence of needle to the end.
     * @return  string|bool                 Returns the portion of string, or FALSE if needle is not found.
     */
    function strstr($string, $needle, $part = false)
    /**/
    {
        return mb_strstr($string, $needle, $part, 'UTF-8');
    }
    
    /**
     * Make a string lowercase.
     *
     * @octdoc  f:string/strtolower
     * @param   string      $string         The string being lowercased.
     * @return  string                      String with all alphabetic characters converted to lowercase.
     */
    function strtolower($string)
    /**/
    {
        return mb_strtolower($string, 'UTF-8');
    }
    
    /**
     * Make a string uppercase.
     *
     * @octdoc  f:string/strtoupper
     * @param   string      $string         The string being uppercased.
     * @return  string                      String with all alphabetic characters converted to uppercase.
     */
    function strtoupper($string)
    /**/
    {
        return mb_strtoupper($string, 'UTF-8');
    }
    
    /**
     * Get part of string.
     *
     * @octdoc  f:string/substr
     * @param   string      $string         The string to extract a part from.
     * @param   int         $start          The first position used in string.
     * @param   int|null    $length         Optional length of the part to extract.
     */
    function substr($string, $start, $length = null)
    /**/
    {
        return mb_substr($string, $start, $length, 'UTF-8');
    }

    /**
     * Count the number of substring occurences.
     *
     * @octdoc  f:string/substr_count
     * @param   string      $string         The string being checked.
     * @param   string      $needle         The string being found.
     * @return  string                      The number of times the needle substring occurs in the haystack string.     
     */
    function substr_count($string, $needle)
    /**/
    {
        return mb_substr_count($string, $needle, 'UTF-8');
    }
    
    /**
     * Convert a specified string to 7bit.
     *
     * @octdoc  f:string/to7bit
     * @param   string      $string         String to convert.
     * @return  string                      Converted string to 7bit.
     */
    function to7bit($string)
    /**/
    {
        $string = mb_convert_encoding($string, 'HTML-ENTITIES', 'UTF-8');
        $string = preg_replace(
            array('/&szlig;/', '/&(..)lig;/', '/&([aouAOU])uml;/','/&(.)[^;]*;/'),
            array('ss', '$1', '$1'.'e', '$1'),
            $string
        );
        
        return $string;
    }
    
    /**
     * Replaces PHP's htmlentities to safely convert using specified encoding.
     *
     * @octdoc  f:string/htmlentities
     * @param   string      $string         String to convert.
     * @return  string                      Converted string.
     */
    function htmlentities($string)
    /**/
    {
    	return \htmlentities($string, ENT_QUOTES, 'UTF-8') ;
    }
    
    /**
     * Convert special characters to HTML entities.
     *
     * @octdoc  f:string/htmlspecialchars
     * @param   string      $string         String to convert.
     * @param   string      $encoding       Optional encoding to use.
     * @return  string                      Converted string.
     */
    function htmlspecialchars($string)
    /**/
    {
        return \htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
    }
    
    /**
     * Convert a string to UTF-8
     *
     * @octdoc  f:string/toUtf8
     * @param   string      $string         String to convert.
     * @param   string      $encoding       Optional convert from this encoding to UTF-8.
     * @return  string                      Converted string.
     */
    function toUtf8($string, $encoding = 'ISO-8859-1')
    /**/
    {
        if (!mb_check_encoding($string, 'UTF-8')) {
            $string = mb_convert_encoding($string, 'UTF-8', $encoding); 
            
            if (!mb_check_encoding($content, 'UTF-8')) {
                trigger_error('unable to convert to UTF-8');
            }
        }

        return $string;
    }
    
    /**
     * Return a formatted string.
     *
     * @octdoc  f:string/sprintf
     * @param   string      $format         Formatting pattern.
     * @param   mixed       $args           Arguments for formatting.
     * @param   mixed       ...             
     * @return  string                      Returns a string produced according to the formatting string format.
     */
    function sprintf($format)
    /**/
    {
        $args = func_get_args();
        array_shift($argv);
        
        return vsprintf($format, $args);
    }
    
    /**
     * Return a formatted string.
     *
     * @octdoc  f:string/vsprintf
     * @param   string      $format         Formatting pattern.
     * @param   mixed       $args           Arguments for formatting.
     * @param   mixed       ...             
     * @return  string                      Returns a string produced according to the formatting string format.
     */
    function vsprintf($format, $args)
    /**/
    {
        $idx = 0;

        $format = preg_replace_callback(
            '/(?<!%)%(\+?)(\'.|[0 ]|)(-?)([1-9][0-9]*|)(\.[1-9][0-9]*|)([bcdeEufFgGosxX])/u',
            function($m) use ($args, &$idx) {
                list($return, $sign, $filler, $align, $size, $prec, $type) = $m;

                if ($type == 's') {
                    // string formatter
                    if (!isset($args[$idx])) {
                        die('argument not set for ' . $idx . "\n");
                    }

                    if (($diff = \strlen($args[$idx]) - strlen($args[$idx], 'UTF-8')) > 0) {
                        if ($prec !== '') $prec = '.' . ((int)\substr($prec, 1) + $diff);
                        if ($size !== '') $size = (int)$size + $diff;
                    }

                    $return = "%$sign$filler$align$size$prec$type";
                }

                ++$idx;

                return $return;
            }, 
            $format
        );

        return \vsprintf($format, $args);
    }
}

/** object-oriented access to multibyte-safe string functions **/
namespace org\octris\core\type {
    use \org\octris\core\type\string as string;
    
    /**
     * String class works internally with UTF-8.
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
         * Original encoding of string.
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
         * @param   string      $encoding       Optional original encoding of string, tries to detect if not specified.
         */
        public function __construct($string, $encoding = null)
        /**/
        {
            if (!mb_check_encoding($string, 'UTF-8')) {
                $this->encoding = mb_detect_encoding($string);
                $this->string   = string\toUtf8($string, $this->encoding);
            } else {
                $this->string = $string;
            }
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
         * Convert a string to UTF-8
         *
         * @octdoc  m:string/toUtf8
         * @return  string                      Converted string.
         */
        function toUtf8()
        /**/
        {
            return new static($string);
        }

        /**
         * Regular expression match for multibyte string.
         *
         * @octdoc  m:string/match
         * @param   string      $pattern        The search pattern.
         * @param   string      $options        If 'i' is specified for this parameter, the case will be ignored.
         * @param   array       $regs           Contains a substring of the matched string.
         * @return  array|bool                  The function returns substring of matched string. If no matches 
         *                                      are found or an error happens, FALSE will be returned.     
         */
        public function match($pattern, $options = '')
        /**/
        {
            return string\match($pattern, $this->string, $options);
        }

        /**
         * Replace regular expression with multibyte support.
         *
         * @octdoc  m:string/replace
         * @param   string      $pattern        The search pattern.
         * @param   string      $options        Matching condition can be set by option parameter. If i is specified for this
         *                                      parameter, the case will be ignored. If x is specified, white space will be
         *                                      ignored. If m is specified, match will be executed in multiline mode and line
         *                                      break will be included in '.'. If p is specified, match will be executed in 
         *                                      POSIX mode, line break will be considered as normal character. If e is 
         *                                      specified, replacement string will be evaluated as PHP expression.
         * @return  string                      The resultant string on success, or FALSE on error.
         */
        public function replace($pattern, $replacement, $string, $options = 'msr')
        /**/
        {
            if (($return = string\replace($pattern, $replacement, $this->string, $options)) !== false) {
                $return = new static($return, $this->encoding);
            }
            
            return $return;
        }

        /**
         * Split multibyte string using regular expression.
         *
         * @octdoc  m:string/split
         * @param   string      $pattern        The regular expression pattern.
         * @return  array                       Array of splitted strings.
         */
        public function split($pattern)
        /**/
        {
            $strings = string\split($pattern, $this->string);
            
            foreach ($strings as &$string) {
                $string = new static($string, $this->encoding);
            }
            
            return $strings;
        }

        /**
         * Finds position of first occurrence of a string within another, case insensitive.
         *
         * @octdoc  m:string/stripos
         * @param   string      $needle         The position counted from the beginning of haystack.
         * @param   int         $offset         The search offset. If it is not specified, 0 is used.
         * @return  int|bool                    Returns the numeric position of the first occurrence of needle in the 
         *                                      haystack string. If needle is not found, it returns FALSE.
         */
        function stripos($needle, $offset = 0)
        /**/
        {
            return string\stripos($this->string, $needle, $offset);
        }

        /**
         * Finds first occurrence of a string within another, case insensitive.
         *
         * @octdoc  m:string/stristr
         * @param   string      $needle         The string to find in string.
         * @param   bool        $part           Determines which portion of haystack this function returns. If set to 
         *                                      TRUE, it returns all of string from the beginning to the first occurrence 
         *                                      of needle. If set to FALSE, it returns all of string from the first
         *                                      occurrence of needle to the end.
         * @return  string|bool                 Returns the portion of string, or FALSE if needle is not found.
         */
        function stristr($needle, $part = false)
        /**/
        {
            if (($return = string\stristr($this->string, $needle, $part)) !== false) {
                $return = new static($return, $this->encoding);
            }
            
            return $return;
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
            return string\strlen($this->string);
        }
        
        /**
         * Pad a string to a certain length with another string.
         *
         * @octdoc  f:string/strpad
         * @param   int         $length         Length to pad string to.
         * @param   string      $chr            Optional character to use for padding.
         * @param   string      $type           Optional argument can be STR_PAD_RIGHT, STR_PAD_LEFT, or STR_PAD_BOTH.
         * @return  string                      Padded string.
         */
        public function strpad($length, $chr = ' ', $type = STR_PAD_RIGHT)
        /**/
        {
            return new static(string\strpad($this->string, $length, $chr, $type), $this->encoding);
        }

        /**
         * Find position of first occurrence of string in a string.
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
            return string\strpos($this->string, $needle, $offset);
        }
        
        /**
         * Reverse a string.
         *
         * @octdoc  m:string/strrev
         * @return  string                      Reversed string.
         */
        public function strrev()
        /**/
        {
            return new static(string\strrev($this->string), $this->encoding);
        }
        
        /**
         * Find position of last occurrence of a string in a string.
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
            return string\strrpos($this->string, $needle, $offset);
        }
        
        /**
         * Finds position of last occurrence of a string within another, case insensitive.
         *
         * @octdoc  m:string/strripos
         * @param   string      $needle         The string to find in haystack.
         * @param   int         $offset         May be specified to begin searching an arbitrary number of characters 
         *                                      into the string. Negative values will stop searching at an arbitrary point
         *                                      prior to the end of the string.
         * @return  int|bool                    Returns the numeric position of the last occurrence of needle in the 
         *                                      haystack string. If needle is not found, it returns FALSE.
         */
        public function strripos($needle, $offset = null)
        /**/
        {
            return string\strripos($this->string, $needle, $offset);
        }

        /**
         * Finds first occurrence of a string within another.
         *
         * @octdoc  m:string/strstr
         * @param   string      $needle         The string to find in string.
         * @param   bool        $part           Determines which portion of haystack this function returns. If set to 
         *                                      TRUE, it returns all of string from the beginning to the first occurrence 
         *                                      of needle. If set to FALSE, it returns all of string from the first
         *                                      occurrence of needle to the end.
         * @return  string|bool                 Returns the portion of string, or FALSE if needle is not found.
         */
        public function strstr($needle, $part = false)
        /**/
        {
            if (($return = string\stristr($this->string, $needle, $part)) !== false) {
                $return = new static($return, $this->encoding);
            }
            
            return $return;
        }

        /**
         * Make a string lowercase.
         *
         * @octdoc  m:string/strtolower
         * @return  string                      String with all alphabetic characters converted to lowercase.
         */
        public function strtolower()
        /**/
        {
            return new static(string\strtolower($this->string), $this->encoding);
        }

        /**
         * Make a string uppercase.
         *
         * @octdoc  m:string/strtoupper
         * @return  string                      String with all alphabetic characters converted to uppercase.
         */
        public function strtoupper()
        /**/
        {
            return new static(string\strtoupper($this->string), $this->encoding);
        }

        /**
         * Get part of string.
         *
         * @octdoc  m:string/substr
         * @param   int         $start          The first position used in string.
         * @param   int|null    $length         Optional length of the part to extract.
         * @return  string                      Extracted part of the string.
         */
        public function substr($start, $length = null)
        /**/
        {
            return new static(string\substr($this->string, $start, $length), $this->encoding);
        }
        
        /**
         * Count the number of substring occurences.
         *
         * @octdoc  m:string/substr_count
         * @param   string      $needle         The string being found.
         * @return  string                      The number of times the needle substring occurs in the haystack string.     
         */
        public function substr_count($needle)
        /**/
        {
            return string\substr_count($this->string, $needle);
        }

        /**
         * Convert a string to 7bit
         *
         * @octdoc  m:string/to7bit
         * @return  string                      Converted string to 7bit.
         */
        public function to7bit()
        /**/
        {
            return string\to7bit($this->string);
        }
        
        /**
         * Safely encode UTF-8 to HTML entities.
         *
         * @octdoc  m:string/htmlentities
         * @return  string                      Converted string.
         */
        public function htmlentities()
        /**/
        {
        	return string\htmlentities($this->string);
        }
        
        /**
         * Convert special characters to HTML entities.
         *
         * @octdoc  m:string/htmlspecialchars
         * @return  string                      Converted string.
         */
        public function htmlspecialchars()
        /**/
        {
            return string\htmlspecialchars($this->string);
        }
    }
}
