<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core {
    /**
     * Debug class.
     * 
     * @octdoc      c:core/debug
     * @copyright   Copyright (c) 2012-2014 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class debug
    /**/
    {
        /**
         * Dump contents of one or multiple variables. This method should not be called directly, use global
         * function 'ddump' instead.
         *
         * @octdoc  m:debug/ddump
         * @param   string      $file               File the ddump command was called from.
         * @param   int         $line               Line number of file the ddump command was called from.
         * @param   ...         $data               Data to dump.
         */
        public static function ddump($file, $line, ...$data)
        /**/
        {
            static $last_key = '';
    
            if (php_sapi_name() != 'cli') {
                print "<pre>";

                $prepare = function($str) {
                    return htmlspecialchars($str);
                };
            } else {
                $prepare = function($str) {
                    return $str;
                };
            }
    
            $key = $file . ':' . $line;
    
            if ($last_key != $key) {
                printf("file: %s\n", $file);
                printf("line: %s\n\n", $line);
                $last_key = $key;
            }
    
            if (extension_loaded('xdebug')) {
                for ($i = 0, $cnt = count($data); $i < $cnt; ++$i) {
                    var_dump($data[$i]);
                }
            } else {
                for ($i = 0, $cnt = count($data); $i < $cnt; ++$i) {
                    ob_start($prepare);
                    var_dump($data[$i]);
                    ob_end_flush();
                }
            }

            if (php_sapi_name() != 'cli') {
                print "</pre>";
            }
        }
        
        /**
         * Print formatted debug message. Message formatting follows the rules of sprints/vsprintf. 
         * This method should not be called directly, use global function 'dprint' instead.
         *
         * @octdoc  m:debug/dprint
         * @param   string      $file               File the ddump command was called from.
         * @param   int         $line               Line number of file the ddump command was called from.
         * @param   string      $msg                Message with optional placeholders to print.
         * @param   mixed       ...$data            Additional optional parameters to print.
         */
        public static function dprint($file, $line, $msg, ...$data)
        /**/
        {
            static $last_key = '';
    
            if (php_sapi_name() != 'cli') {
                print "<pre>";

                $prepare = function($str) {
                    return htmlspecialchars($str);
                };
            } else {
                $prepare = function($str) {
                    return $str;
                };
            }
    
            $key = $file . ':' . $line;
    
            if ($last_key != $key) {
                printf("file: %s\n", $file);
                printf("line: %s\n\n", $line);
                $last_key = $key;
            }

            ob_start($prepare);
            vprintf($msg, $data);
            ob_end_flush();

            if (php_sapi_name() != 'cli') {
                print "</pre>";
            }
        }
    }
}

namespace {
    use \org\octris\core\debug as dbg;
    
    /**
     * Dump contents of one or multiple variables.
     *
     * @octdoc  f:debug/ddump
     * @param   mixed         ...$params        Parameters to pass to \org\octris\core\debug::ddump.
     */
    function ddump(...$params)
    /**/
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1)[0];

        dbg::ddump($trace['file'], $trace['line'], ...$params);
    }

    /**
     * Print formatted debug message. Message formatting follows the rules of sprints/vsprintf.
     *
     * @octdoc  m:debug/dprint
     * @param   string      $msg                Message with optional placeholders to print.
     * @param   mixed       ...$params          Parameters to pass to \org\octris\core\debug::dprint.
     */
    function dprint($msg, ...$params)
    /**/
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1)[0];

        dbg::dprint($trace['file'], $trace['line'], $msg, ...$params);
    }
}
