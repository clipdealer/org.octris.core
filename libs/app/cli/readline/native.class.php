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
     * @copyright   copyright (c) 2011-2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     *
     * @depends     \org\octris\core\app\cli\readline
     * @depends     \org\octris\core\app\cli\readline_if
     */
    class native implements \org\octris\core\app\cli\readline_if
    /**/
    {
        /**
         * Instance counter.
         *
         * @octdoc  p:native/$instances
         * @type    int
         */
        private static $instances = 0;
        /**/
        
        /**
         * Instance number.
         *
         * @octdoc  p:native/$instance_id
         * @type    int
         */
        private $instance_id = 0;
        /**/
        
        /**
         * Whether the readline implementation supports an input history.
         *
         * @octdoc  p:native/$has_history
         * @type    bool
         */
        protected static $has_history = false;
        /**/

        /**
         * Name of history file that was used for previous call to readline.
         *
         * @octdoc  p:native/$last_history
         * @type    string
         */
        private static $last_history = '';
        /**/
        
        /**
         * Last used instance.
         *
         * @octdoc  p:native/$last_instance
         * @type    int
         */
        private static $last_instance = 0;
        /**/
        
        /**
         * Completion function.
         *
         * @octdoc  p:native/$completion_callback
         * @type    null|callable
         */
        protected $completion_callback = null;
        /**/
        
        /**
         * History file bound to instance of readline. If no file is specified, the history will not be used.
         *
         * @octdoc  p:native/$history_file
         * @type    string
         */
        protected $history_file = '';
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:native/__construct
         * @param   string          $history_file               History file to use for this readline instance.
         */
        public function __construct($history_file = '')
        /**/
        {
            $this->history_file = (self::$has_history ? $history_file : '');
            $this->instance_id  = ++self::$instances;
        }

        /**
         * Detect native readline support.
         *
         * @octdoc  m:native/detect
         * @return  bool                    Whether native readline is supported.
         */
        public static function detect()
        /**/
        {
            $history = false;
            
            if (($detected = function_exists('readline'))) {
                self::$has_history = function_exists('readline_read_history');
            }
            
            return $detected;
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
         * Register a completion function.
         *
         * @octdoc  m:native/setCompletion
         * @param   callable        $callback               Callback to call for completion.
         */
        public function setCompletion(callable $callback)
        /**/
        {
            $this->completion_callback = $callback;
        }

        /**
         * Switch readline instance settings. Changes history if there are multiple 
         * readline instances with different history files and changes completion callback for 
         * different readline instances.
         *
         * @octdoc  m:native/switchSettings
         */
        protected function switchSettings()
        /**/
        {
            if ($this->instance_id != self::$last_instance) {
                // switch instance settings
                if (is_null($this->completion_callback)) {
                    readline_completion_function(function($input, $index) {});
                } else {
                    readline_completion_function(function($input, $index) {
                        return $this->complete($input, $index, $this->completion_callback);
                    });
                }
                
                if ($this->history_file != self::$last_history) {
                    // change history
                    readline_write_history(self::$last_file);
                    readline_clear_history();
                
                    if ($this->history_file != '') {
                        readline_read_history($this->history_file);
                    }
                
                    self::$last_history = $this->history_file;
                }

                self::$last_instance = $this->instance_id;
            }
        }
        
        /**
         * Add string to the history file.
         *
         * @octdoc  m:native/addHistory
         * @param   string      $line       Line to add to the history file.
         */
        protected function addHistory($line)
        /**/
        {
            if ($this->history_file) {
                readline_add_history($line);
            }
        }
        
        /**
         * Completion main function.
         *
         * @octdoc  m:native/complete
         * @param   string      $input      Input from readline.
         * @param   string      $index      Position in line where completion was initiated.
         * @param   callable    $callback   A callback to call for processing completion.
         * @return  array                   Matches.
         */
        public function complete($input, $index, callable $callback)
        /**/
        {
            $info = readline_info();
            $line = substr($info['line_buffer'], 0, $info['end']);

            foreach ($callback($input, $line) as $match) {
                $matches[] = substr($match, $index);
            }
            
            return $matches;
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
            $this->switchSettings();
            
            $return = ltrim(\readline($prompt));
            
            $this->addHistory($return);
            
            return $return;
        }
    }
}
