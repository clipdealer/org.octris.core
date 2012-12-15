<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Class with helper methods for debugging.
 * 
 * @octdoc      c:core/octdebug
 * @copyright   Copyright (c) 2012 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class octdebug
/**/
{
    /**
     * Dump contents of one or multiple variables.
     *
     * @octdoc  m:debug/dump
     * @param   mixed       $data               Data to dump.
     * @param   ...
     */
    public static function dump($data)
    /**/
    {
        if (php_sapi_name() != 'cli') {
            print "<hr/><pre>";

            $prepare = function($str) {
                return htmlspecialchars($str);
            };
        } else {
            $prepare = function($str) {
                return $str;
            };
        }
        
        $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1)[0];
        
        printf("file: %s\n", $trace['file']);
        printf("line: %s\n\n", $trace['line']);
        
        for ($i = 0, $cnt = func_num_args(); $i < $cnt; ++$i) {
            ob_start($prepare);
            var_dump(func_get_arg($i));
            ob_end_flush();
        }

        if (php_sapi_name() != 'cli') {
            print "</pre><hr/>";
        }
    }
}
