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
     * Return a specific character.
     *
     * @octdoc  f:string/chr
     * @param   int         $chr            Code of the character to return.
     * @return  string                      The specified character.
     */
    function chr($chr)
    /**/
    {
        return mb_convert_encoding('&#' . (int)$chr . ';', 'UTF-8', 'HTML-ENTITIES');
    }
        
    /**
     * Split a string into smaller chunks.
     *
     * @octdoc  f:string/chunk_split
     * @param   string      $string         The string to be chunked.
     * @param   int         $chunklen       The chunk length.
     * @param   string      $end            The line ending sequence.
     * @return  string                      The chunked string.
     */
    function chunk_split($string, $chunklen = 76, $end = "\r\n")
    /**/
    {
        return preg_replace_callback('/.{' . $chunklen . '}/us', function($m) use ($end) {
            return $m[0] . $end;
        }, $str) . (mb_strlen($str, 'UTF-8') % $len == 0 ? '' : $end);
    }

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
     * Pad a string to a certain length with another string.
     *
     * @octdoc  f:string/str_pad
     * @param   string      $string         String to pad.
     * @param   int         $length         Length to pad string to.
     * @param   string      $chr            Optional character to use for padding.
     * @param   string      $type           Optional argument can be STR_PAD_RIGHT, STR_PAD_LEFT, or STR_PAD_BOTH.
     * @return  string                      Padded string.
     */
    function str_pad($string, $length, $chr = ' ', $type = STR_PAD_RIGHT)
    /**/
    {
        if (!in_array($type, array(STR_PAD_LEFT, STR_PAD_RIGHT, STR_PAD_BOTH))) {
            $type = STR_PAD_RIGHT;
        }
        
        $diff = strlen($string) - mb_strlen($string, 'UTF-8');

        return str_pad($string, $length + $diff, $chr, $type);
    }

    /**
     * Replace all occurrences of the search string with the replacement string.
     *
     * @octdoc  f:string/str_replace
     * @param   string      $search         The value being searched for, otherwise known as the needle. An array may be used to designate multiple needles.
     * @param   string      $replace        The replacement value that replaces found search values. An array may be used to designate multiple replacements.
     * @param   string      $subject        The string or array being searched and replaced on, otherwise known as the haystack. If subject is an array, 
     *                                      then the search and replace is performed with every entry of subject, and the return value is an array as well.
     * @param   int         $count          If passed, this will be set to the number of replacements performed.
     * @return  string                      This function returns a string or an array with the replaced values.
     */
    function str_replace($search, $replace, $subject, &$count = null)
    /**/
    {
        return str_replace($search, $replace, $subject, $count);
    }
    
    /**
     * Randomly shuffles a string.
     *
     * @octdoc  f:string/str_shuffle
     * @param   string      $string         The string to shuffle.
     * @return  string                      The shuffled string.
     */
    function str_shuffle($string)
    /**/
    {
        return implode('', array_shuffle(preg_split('//us', $string)));
    }
    
    /**
     * Convert a string to an array.
     *
     * @octdoc  f:string/str_split
     * @param   string      $string         The string to be chunked.
     * @param   int         $split_length   Optional maximum length of the chunk.
     * @return  array                       The chunked string.
     */
    function str_split($string, $split_length = 1)
    /**/
    {
        $m = array();
        
        return (preg_match_all('/.{1,' . $len . '}/us', $str, $m)
                ? $m[0]
                : array());
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
     * Replace text within a portion of a string.
     *
     * @octdoc  f:string/substr_replace
     * @param   string      $string         The input string.
     * @param   string      $replacement    The replacement string.
     * @param   int         $start          If start is positive, the replacing will begin at the start'th offset 
     *                                      into string. If start is negative, the replacing will begin at the 
     *                                      start'th character from the end of string.
     * @param   int         $length         Optional length of portion of string which shall be replaced. If length 
     *                                      is negative, it represents the number of characters from the end of string 
     *                                      at which to stop replacing. If it is not given, then it will default to 
     *                                      the length of the string. If length is zero then this function will have 
     *                                      the effect of inserting replacement into string at the given start offset.
     */
    function substr_replace($string, $replacement, $start, $length = null)
    /**/
    {
        if (is_null($length)) $length = strlen($string);
        
        return substr($string, 0, $start) . 
                $replacement . 
                substr($string, ($len < 0 ? max($start - strlen($string), $length) : $start + $length));
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
     * @param   int         $quote_style    Optional parameter to define what will be done with 'single' and "double" quotes.
     * @return  string                      Converted string.
     */
    function htmlentities($string, $quote_style = ENT_COMPAT)
    /**/
    {
    	return \htmlentities($string, $quote_style, 'UTF-8') ;
    }
    
    /**
     * 
     *
     * @octdoc  f:string/html_entity_decode
     * @param   string      $string         The input string.
     * @param   int         $quote_style    Optional parameter to define what will be done with 'single' and "double" quotes.
     * @return  string
     */
    function html_entity_decode($string, $quote_style = ENT_COMPAT)
    /**/
    {
        return \html_entity_decode($string, $quote_style, 'UTF-8');
    }
    
    
    /**
     * Convert special characters to HTML entities.
     *
     * @octdoc  f:string/htmlspecialchars
     * @param   string      $string         String to convert.
     * @param   int         $quote_style    Optional parameter to define what will be done with 'single' and "double" quotes.
     * @return  string                      Converted string.
     */
    function htmlspecialchars($string, $quote_style)
    /**/
    {
        return \htmlspecialchars($string, $quote_style, 'UTF-8');
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
     * Convert character encoding of a string.
     *
     * @octdoc  f:string/convert
     * @param   string      $string         The string being encoded.
     * @param   string      $to_encoding    The type of encoding that str is being converted to.
     * @param   string      $from_encoding  Optional source encoding is specified by character code names before conversion. 
     *                                      It is either an array, or a comma separated enumerated list. If from_encoding is not
     *                                      specified, the internal encoding will be used.
     * @return  string                      The encoded string.
     */
    function convert($string, $to_encoding, $from_encoding = null)
    /**/
    {
        return mb_convert_encoding($string, $to_encoding, $from_encoding);
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
