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
     * Static class providing UTF-8 safe string functions.
     *
     * @octdoc      c:type/string
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class string
    /**/
    {
        /** make class static **/
        protected function __construct() {}
        
        /**
         * Return a specific character.
         *
         * @octdoc  f:string/chr
         * @param   int         $chr            Code of the character to return.
         * @return  string                      The specified character.
         */
        public static function chr($chr)
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
        public static function chunk_split($string, $chunklen = 76, $end = "\r\n")
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
         * @return  array|bool                  The public static function returns substring of matched string. If no matches 
         *                                      are found or an error happens, FALSE will be returned.     
         */
        public static function match($pattern, $string, $options = '')
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
        public static function replace($pattern, $replacement, $string, $options = 'msr')
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
        public static function split($pattern, $string)
        /**/
        {
            mb_regex_encoding('UTF-8');
        
            return mb_split($pattern, $string);
        }
    
        /**
         * Binary safe case-insensitive string comparison.
         *
         * @octdoc  f:string/strcasecmp
         * @param   string      $string1        The first string.
         * @param   string      $string2        The second string.
         * @return  int                         Returns < 0 if string1 is less than string2.
         *                                      Returns > 0 if string1 is greater than string2
         *                                      Returns 0 if both strings are equal.
         */
        public static function strcasecmp($string1, $string2)
        /**/
        {
            return strcmp(strtolower($string1), strtolower($string2));
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
        public static function stripos($string, $needle, $offset = 0)
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
         * @param   bool        $part           Determines which portion of haystack this public static function returns. If set to 
         *                                      TRUE, it returns all of string from the beginning to the first occurrence 
         *                                      of needle. If set to FALSE, it returns all of string from the first
         *                                      occurrence of needle to the end.
         * @return  string|bool                 Returns the portion of string, or FALSE if needle is not found.
         */
        public static function stristr($string, $needle, $part = false)
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
        public static function strlen($string)
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
        public static function strpos($string, $needle, $offset = 0)
        /**/
        {
            return mb_strpos($string, $needle, $offset, 'UTF-8');
        }
    
        /**
         * Binary safe case-insensitive string comparison using natural sorting algorithm.
         *
         * @octdoc  f:string/strnatcasecmp
         * @param   string      $string1        The first string.
         * @param   string      $string2        The second string.
         * @return  int                         Returns < 0 if string1 is less than string2.
         *                                      Returns > 0 if string1 is greater than string2
         *                                      Returns 0 if both strings are equal.
         */
        public static function strnatcasecmp($string1, $string2)
        /**/
        {
            return strnatcmp(strtolower($string1), strtolower($string2));
        }
    
        /**
         * Binary safe case-insensitive string comparison of the first n characters.
         *
         * @octdoc  f:string/strncasecmp
         * @param   string      $string1        The first string.
         * @param   string      $string2        The second string.
         * @param   int         $length         Number of characters to use in the comparison.
         * @return  int                         Returns < 0 if string1 is less than string2.
         *                                      Returns > 0 if string1 is greater than string2
         *                                      Returns 0 if both strings are equal.
         */
        public static function strncasecmp($string1, $string2, $length)
        /**/
        {
            return strncmp(strtolower($string1), strtolower($string2), $length);
        }
    
        /**
         * Binary safe string comparison of the first n characters.
         *
         * @octdoc  f:string/strncmp
         * @param   string      $string1        The first string.
         * @param   string      $string2        The second string.
         * @param   int         $length         Number of characters to use in the comparison.
         * @return  int                         Returns < 0 if string1 is less than string2.
         *                                      Returns > 0 if string1 is greater than string2
         *                                      Returns 0 if both strings are equal.
         */
        public static function strncmp($string1, $string2, $length)
        /**/
        {
            $string1 = substr($string1, 0, $length);
            $string2 = substr($string2, 0, $length);
        
            return strcmp($string1, $string2);
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
        public static function str_pad($string, $length, $chr = ' ', $type = STR_PAD_RIGHT)
        /**/
        {
            if (!in_array($type, array(STR_PAD_LEFT, STR_PAD_RIGHT, STR_PAD_BOTH))) {
                $type = STR_PAD_RIGHT;
            }
        
            $diff = strlen($string) - mb_strlen($string, 'UTF-8');

            return str_pad($string, $length + $diff, $chr, $type);
        }

        /**
         * Randomly shuffles a string.
         *
         * @octdoc  f:string/str_shuffle
         * @param   string      $string         The string to shuffle.
         * @return  string                      The shuffled string.
         */
        public static function str_shuffle($string)
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
        public static function str_split($string, $split_length = 1)
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
        public static function strrev($string)
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
        public static function strrpos($string, $needle, $offset = null)
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
        public static function strripos($string, $needle, $offset = null)
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
         * @param   bool        $part           Determines which portion of haystack this public static function returns. If set to 
         *                                      TRUE, it returns all of string from the beginning to the first occurrence 
         *                                      of needle. If set to FALSE, it returns all of string from the first
         *                                      occurrence of needle to the end.
         * @return  string|bool                 Returns the portion of string, or FALSE if needle is not found.
         */
        public static function strstr($string, $needle, $part = false)
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
        public static function strtolower($string)
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
        public static function strtoupper($string)
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
        public static function substr($string, $start, $length = null)
        /**/
        {
            return mb_substr($string, $start, $length, 'UTF-8');
        }

        /**
         * Binary safe comparison of two strings from an offset, up to length characters.
         *
         * @octdoc  f:string/substr_compare
         * @param   string      $string         The main string being compared.
         * @param   string      $compare        The secondary string being compared.
         * @param   int         $offset         The start position for the comparison. If negative, it starts counting from 
         *                                      the end of the string.
         * @param   int         $length         Optional length of the comparison. The default value is the largest of the length
         *                                      of $string compared to the length of $compare less the offset.
         * @param   bool        $ignore_case    Optional, if set to TRUE, comparison is case insensitive.
         */
        public static function substr_compare($string, $compare, $offset, $length = null, $ignore_case = false)
        /**/
        {
            if (is_null($length)) {
                $string = mb_substr($string, $offset);
            } else {
                $string  = mb_substr($string, $offset, $length);
                $compare = mb_substr($string, 0, $length);
            }
        
            return ($ignore_case ? strcasecmp($string, $compare) : strcmp($string, $compare));
        }

        /**
         * Count the number of substring occurences.
         *
         * @octdoc  f:string/substr_count
         * @param   string      $string         The string being checked.
         * @param   string      $needle         The string being found.
         * @return  string                      The number of times the needle substring occurs in the haystack string.     
         */
        public static function substr_count($string, $needle)
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
         *                                      the length of the string. If length is zero then this public static function will have 
         *                                      the effect of inserting replacement into string at the given start offset.
         */
        public static function substr_replace($string, $replacement, $start, $length = null)
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
        public static function to7bit($string)
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
        public static function htmlentities($string, $quote_style = ENT_COMPAT)
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
        public static function html_entity_decode($string, $quote_style = ENT_COMPAT)
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
        public static function htmlspecialchars($string, $quote_style)
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
        public static function toUtf8($string, $encoding = 'ISO-8859-1')
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
        public static function convert($string, $to_encoding, $from_encoding = null)
        /**/
        {
            return mb_convert_encoding($string, $to_encoding, $from_encoding);
        }
    
        /**
         * Strip whitespace (or other characters) from the beginning of a string.
         *
         * @octdoc  f:string/ltrim
         * @param   string      $string         The input string.
         * @param   string      $charlist       Optional characters to strip.
         * @return  string                      Stripped string.
         */
        public static function ltrim($string, $charlist = null)
        /**/
        {
            if (is_null($charlist)) {
                $string = ltrim($string);
            } else {
                $regexp = '/^[' . preg_quote($charlist, '/') . ']+/u';
                $string = preg_replace($regexp, '', $string);
            }
        
            return $string;
        }
    
        /**
         * Strip whitespace (or other characters) from the end of a string.
         *
         * @octdoc  f:string/rtrim
         * @param   string      $string         The input string.
         * @param   string      $charlist       Optional characters to strip.
         * @return  string                      Stripped string.
         */
        public static function rtrim($string, $charlist = null)
        /**/
        {
            if (is_null($charlist)) {
                $string = rtrim($string);
            } else {
                $regexp = '/[' . preg_quote($charlist, '/') . ']+$/u';
                $string = preg_replace($regexp, '', $string);
            }
        
            return $string;
        }
    
        /**
         * Strip whitespace (or other characters) from the both start and end of a string.
         *
         * @octdoc  f:string/trim
         * @param   string      $string         The input string.
         * @param   string      $charlist       Optional characters to strip.
         * @return  string                      Stripped string.
         */
        public static function trim($string, $charlist = null)
        /**/
        {
            if (is_null($charlist)) {
                $string = trim($string);
            } else {
                $string = ltrim(rtrim($string, $charlist), $charlist);
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
        public static function sprintf($format)
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
        public static function vsprintf($format, $args)
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
    
        /**
         * Check if a specified string is valid UTF-8.
         *
         * @octdoc  f:string/isUtf8
         * @param   string      $string         String to validate.
         * @return  bool                        Returns true, if a string is valid UTF-8.
         */
        public static function isUtf8($string)
        /**/
        {
            $tmp = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
        
            return ($tmp == $string);
        
        }
    }
}
