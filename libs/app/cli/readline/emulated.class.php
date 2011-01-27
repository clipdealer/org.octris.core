<?php

namespace org\octris\core\app\cli\readline {
    /**
     * Emulated readline support, should be only used, if built-in
     * readline-support or readline using bash is not available. The
     * emulated readline support does not support a history file.
     *
     * @octdoc      c:readline/emulated
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class emulated extends \org\octris\core\app\cli\readline
    /**/
    {
        /**
         * Detect emulated readline support. This is only a dummy, because emulated readline is
         * always available.
         *
         * @octdoc  m:native/detect
         * @return  array                   Returns an array with two boolean values.
         */
        public static function detect()
        /**/
        {
            return array(true, false);
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
            print $prompt;
            
            if (($fh = fopen('php://stdin', 'r'))) {
                $return = ltrim(rtrim(fgets($fh), "\r\n"));
                fclose($fh);
            }
            
            return $return;
        }
    }
}
