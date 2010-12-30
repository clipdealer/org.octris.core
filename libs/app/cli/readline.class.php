<?php

namespace org\octris\core\app\cli {
    /****c* cli/readline
     * NAME
     *      readline
     * FUNCTION
     *      readline wrapper and fallback, if no readline is available
     * COPYRIGHT
     *      copyright (c) 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */

    class readline {
        /****m* readline/__construct
         * SYNOPSIS
         */
        public function __construct()
        /*
         * FUNCTION
         *      constructor
         ****
         */
        {
        }
        
        /****m* readline/get
         * SYNOPSIS
         */
        public function get($prompt = '', $default = '', $force = false)
        /*
         * FUNCTION
         *      get user input from stdin
         * INPUTS
         *      * $prompt (string) -- (optional) prompt to print
         *      * $default (string) -- (optional) default value
         *      * $force (bool) -- (optional) whether to force input
         * OUTPUTS
         *      (string) -- read user input
         ****
         */
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
