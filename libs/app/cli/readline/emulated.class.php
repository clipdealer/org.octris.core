<?php

namespace org\octris\core\app\cli\readline {
    /**
     * Emulated readline support, should be only used, if built-in 
     * readline-support is not available.
     *
     * @octdoc      c:readline/emulated
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class emulated extends \org\octris\core\app\cli\readline
    /**/
    {
        /**
         * Get user input from STDIN.
         *
         * @octdoc  m:readline/get
         * @param   string      $prompt     Optional prompt to print.
         * @param   string      $default    Optional default value.
         * @param   bool        $force      Optional flag to indicate whether to
         *                                  force input.
         * @return  string                  User input.
         */
        public function get($prompt = '', $default = '', $force = false)
        /**/
        {
            $return   = false;
            $iterator = 3;
            
            do {
                printf($prompt, $default);

                if (($fh = fopen('php://stdin', 'r'))) {
                    $return = rtrim(fgets($fh), "\r\n");
                    fclose($fh);
                }
                
                $return = ($return == '' ? $default : trim($return));
                --$iterator;
            } while($force && $return == '' && $iterator > 0);
            
            return $return;
        }
    }
}
