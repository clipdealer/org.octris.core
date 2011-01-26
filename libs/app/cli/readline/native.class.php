<?php

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
         * Get user input from STDIN.
         *
         * @octdoc  m:native/get
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
                $return = readline(sprintf($prompt, $default));
                $return = ($return == '' ? $default : trim($return));

                --$iterator;
            } while($force && $return == '' && $iterator > 0);
            
            return $return;
        }
    }
}
