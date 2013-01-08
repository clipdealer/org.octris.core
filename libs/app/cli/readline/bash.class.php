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
     * Use bash for readline support.
     *
     * @octdoc      c:readline/bash
     * @copyright   copyright (c) 2011-2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     *
     * @depends     \org\octris\core\app\cli\readline
     * @depends     \org\octris\core\app\cli\readline_if
     */
    class bash implements \org\octris\core\app\cli\readline_if
    /**/
    {
        /**
         * Whether the readline implementation supports an input history.
         *
         * @octdoc  p:native/$has_history
         * @var     bool
         */
        protected static $has_history = false;
        /**/

        /**
         * Set bash command.
         *
         * @octdoc  p:bash/$cmd
         * @var     string
         */
        protected static $cmd = '';
        /**/

        /**
         * History file bound to instance of readline. If no file is specified, the history will not be used.
         *
         * @octdoc  p:bash/$history_file
         * @var     string
         */
        protected $history_file = '';
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:bash/__construct
         * @param   string          $history_file               History file to use for this readline instance.
         */
        public function __construct($history_file = '')
        /**/
        {
            $this->history_file = (self::$has_history ? $history_file : '');
        }

        /**
         * Detect bash readline support.
         *
         * @octdoc  m:bash/detect
         * @return  bool                    Whether readline using bash is supported.
         */
        public static function detect()
        /**/
        {
            if (($detected = !!($cmd = exec('which bash')))) {
                if (($detected = (preg_match('/builtin/', exec($cmd . ' -c "type type"')) &&
                                  preg_match('/builtin/', exec($cmd . ' -c "type read"'))))) {
                    self::$has_history = preg_match('/builtin/', exec($cmd . ' -c "type history"'));

                    self::$cmd = $cmd;
                }
            }

            return $detected;
        }

        /**
         * Register a completion function.
         *
         * @octdoc  m:bash/setCompletion
         * @param   callable        $callback               Callback to call for completion.
         */
        public function setCompletion(callable $callback)
        /**/
        {
        }

        /**
         * Get user input from STDIN.
         *
         * @octdoc  m:bash/readline
         * @param   string      $prompt     Optional prompt to print.
         * @return  string                  User input.
         */
        public function readline($prompt = '')
        /**/
        {
            if ($this->history_file != '') {
                // input supports history
                $cmd = sprintf(
                    '%s -c "history -r %s; CMD=""; read -ep %s CMD; history -s \$CMD; history -w %s; echo \$CMD"',
                    self::$cmd,
                    escapeshellarg($this->history_file),
                    escapeshellarg($prompt),
                    escapeshellarg($this->history_file)
                );
            } else {
                // input does not support history
                $cmd = sprintf(
                    '%s -c "CMD=""; read -ep %s CMD; echo \$CMD"',
                    self::$cmd,
                    escapeshellarg($prompt)
                );
            }

            $return = exec($cmd);

            return $return;
        }
    }
}
