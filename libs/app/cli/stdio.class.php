<?php

namespace org\octris\core\app\cli {
    /**
     * Input/output functionality for cli applications.
     *
     * @octdoc      c:cli/stdio
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class stdio
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