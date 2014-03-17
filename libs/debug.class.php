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
         * Dump contents of one or multiple variables.
         *
         * @octdoc  m:debug/ddump
         * @param   mixed       $data               Data to dump.
         * @param   ...         ...                 Additional optional parameters to dump.
         */
        public static function ddump($data)
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
    
            $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1)[0];
            $key   = $trace['file'] . ':' . $trace['line'];
    
            if ($last_key != $key) {
                printf("file: %s\n", $trace['file']);
                printf("line: %s\n\n", $trace['line']);
                $last_key = $key;
            }
    
            if (extension_loaded('xdebug')) {
                for ($i = 0, $cnt = func_num_args(); $i < $cnt; ++$i) {
                    var_dump(func_get_arg($i));
                }
            } else {
                for ($i = 0, $cnt = func_num_args(); $i < $cnt; ++$i) {
                    ob_start($prepare);
                    var_dump(func_get_arg($i));
                    ob_end_flush();
                }
            }

            if (php_sapi_name() != 'cli') {
                print "</pre>";
            }
        }
        
        /**
         * Print formatted debug message. Message formatting follows the rules of sprints/vsprintf.
         *
         * @octdoc  m:debug/dprint
         * @param   string      $msg                Message with optional placeholders to print.
         * @param   ...         ...                 Additional optional parameters to print.
         */
        public static function dprint($msg, ...$data)
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
    
            $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1)[0];
            $key   = $trace['file'] . ':' . $trace['line'];
    
            if ($last_key != $key) {
                printf("file: %s\n", $trace['file']);
                printf("line: %s\n\n", $trace['line']);
                $last_key = $key;
            }

            ob_start($prepare);
            printf($msg, ...$data);
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
     * Dump contents of one or multiple variables, shortcut for \org\octris\core\debug::ddump
     *
     * @octdoc  f:debug/ddump
     * @param   mixed       $data               Data to dump.
     * @param   ...         ...                 Additional optional parameters to dump.
     */
    function ddump(...$params)
    /**/
    {
        dbg::ddump(...$params);
    }

    /**
     * Print formatted debug message. Message formatting follows the rules of sprints/vsprintf.
     *
     * @octdoc  m:debug/dprint
     * @param   string      $msg                Message with optional placeholders to print.
     * @param   ...         ...                 Additional optional parameters to print.
     */
    function dprint(...$params)
    /**/
    {
        dbg::dprint(...$params);
    }
}
