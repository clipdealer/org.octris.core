<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\app\cli {
    /**
     * Input/output functionality for cli applications.
     *
     * @octdoc      c:cli/stdio
     * @copyright   copyright (c) 2011-2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     *
     * @depends     \org\octris\core\app\cli\readline
     */
    class stdio
    /**/
    {
        /**
         * Print a horizontal line of characters.
         *
         * @octdoc  m:stdio/hline
         * @param   string      $chr        Optional character to use for printing line.
         */
        public static function hline($chr = '=')
        /**/
        {
            $cols = (int)`tput cols`;
            $cols = ($cols > 0 ? $cols : 80);
            
            print substr(str_repeat($chr, $cols), 0, $cols) . "\n";
        }

        /**
         * Clear screen.
         *
         * @octdoc  m:stdio/clear
         */
        public static function clear()
        /**/
        {
            foreach (array(27, 91, 72, 27, 91, 50, 74) as $code) {
                print chr($code);
            }
        }

        /**
         * Get user input from STDIN.
         *
         * @octdoc  m:stdio/get
         * @param   string      $prompt     Optional prompt to print.
         * @param   string      $default    Optional default value.
         * @param   bool        $force      Optional flag to indicate whether to
         *                                  force input.
         * @return  string                  User input.
         */
        public static function getPrompt($prompt = '', $default = '', $force = false)
        /**/
        {
            $readline = \org\octris\core\app\cli\readline::getInstance();
            
            $return   = false;
            $iterator = 3;
            
            do {
                $return = $readline->readline(sprintf($prompt, $default));

                $return = ($return == '' ? $default : trim($return));
                --$iterator;
            } while($force && $return == '' && $iterator > 0);
            
            return $return;
        }
    }
}