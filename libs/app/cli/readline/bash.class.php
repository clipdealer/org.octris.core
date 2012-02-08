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
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class bash extends \org\octris\core\app\cli\readline
    /**/
    {
        /**
         * Set bash command.
         *
         * @octdoc  p:bash/$cmd
         * @var     string
         */
        private static $cmd = '';
        /**/
        
        /**
         * Detect bash readline support.
         *
         * @octdoc  m:native/detect
         * @return  array                   Returns an array with two boolean values.
         */
        public static function detect()
        /**/
        {
            $history = false;
            
            if (!!($cmd = exec('which bash'))) {
                if (($detected = (preg_match('/builtin/', exec($cmd . ' -c "type type"')) && 
                                  preg_match('/builtin/', exec($cmd . ' -c "type read"'))))) {
                    $history = preg_match('/builtin/', exec($cmd . ' -c "type history"'));

                    self::$cmd = $cmd;
                }
            }
            
            return array($detected, $history);
        }
        
        /**
         * Get user input from STDIN.
         *
         * @octdoc  m:native/readline
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
