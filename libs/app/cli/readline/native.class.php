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
     * Wrapper for native (built-in) readline functionality.
     *
     * @octdoc      c:readline/native
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class native extends \org\octris\core\app\cli\readline
    /**/
    {
        /**
         * Name of history file that was used for previous call to readline.
         *
         * @octdoc  v:native/$last_history
         * @var     string
         */
        private static $last_history = '';
        /**/
        
        /**
         * Detect native readline support.
         *
         * @octdoc  m:native/detect
         * @return  mixed                   Returns either bool false, if native readline support is available or an array with
         *                                  two boolean values. First one is true, second one is set to true only, if history is
         *                                  supported by readline driver.
         */
        public static function detect()
        /**/
        {
            $history = false;
            
            if (($detected = function_exists('readline'))) {
                $history = function_exists('readline_read_history');
            }
            
            return ($detected ? array(true, $history) : false);
        }
        
        /**
         * Destructor writes history to history file.
         *
         * @octdoc  m:native/__destruct
         */
        public function __destruct()
        /**/
        {
            if ($this->history_file != '') {
                readline_write_history($this->history_file);
            }
        }
        
        /**
         * Change history.
         *
         * @octdoc  m:native/switchHistory
         */
        private function switchHistory()
        /**/
        {
            if ($this->history_file != self::$last_history) {
                if ($this->history_file == '') {
                    readline_clear_history();
                } else {
                    readline_read_history($this->history_file);
                }
                
                self::$last_history = $this->history_file;
            }
        }
        
        /**
         * Add string to the history file.
         *
         * @octdoc  m:native/addHistory
         * @param   string      $line       Line to add to the history file.
         */
        public function addHistory($line)
        /**/
        {
            if ($this->history_file) {
                readline_add_history($line);
            }
        }
        
        /**
         * Get user input using native readline extension.
         *
         * @octdoc  m:native/readline
         * @param   string      $prompt     Optional prompt to print.
         * @return  string                  User input.
         */
        public function readline($prompt = '')
        /**/
        {
            $this->switchHistory();
            
            $return = ltrim(\readline($prompt));
            
            $this->addHistory($return);
            
            return $return;
        }
    }
}
