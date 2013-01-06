<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\app\cli\readline {
    /**
     * Emulated readline support, should be only used, if built-in
     * readline-support or readline using bash is not available. The
     * emulated readline support does not support a history file.
     *
     * @octdoc      c:readline/emulated
     * @copyright   copyright (c) 2011-2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     *
     * @depends     \org\octris\core\app\cli\readline
     * @depends     \org\octris\core\app\cli\readline_if
     */
    class emulated implements \org\octris\core\app\cli\readline_if
    /**/
    {
        /**
         * Constructor.
         *
         * @octdoc  m:emulated/__construct
         * @param   string          $history_file               History file to use for this readline instance.
         */
        public function __construct($history_file = '')
        /**/
        {
        }

        /**
         * Detect emulated readline support. This is only a dummy, because emulated readline is
         * always available.
         *
         * @octdoc  m:emulated/detect
         * @return  bool                    Returns always true.
         */
        public static function detect()
        /**/
        {
            return true;
        }
        
        /**
         * Register a completion function.
         *
         * @octdoc  m:emulated/setCompletion
         * @param   callable        $callback               Callback to call for completion.
         */
        public function setCompletion(callable $callback)
        /**/
        {
        }

        /**
         * Get user input from STDIN.
         *
         * @octdoc  m:emulated/readline
         * @param   string      $prompt     Optional prompt to print.
         * @return  string                  User input.
         */
        public function readline($prompt = '')
        /**/
        {
            print $prompt;
            
            if (($fh = fopen('php://stdin', 'r'))) {
                $return = ltrim(rtrim(fgets($fh), "\r\n"));
                fclose($fh);
            }
            
            return $return;
        }
    }
}
