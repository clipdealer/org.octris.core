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
        public function get($prompt = '')
        /*
         * FUNCTION
         *      get user input from stdin
         * INPUTS
         *      * $prompt (string) -- (optional) prompt to print
         * OUTPUTS
         *      (string) -- read user input
         ****
         */
        {
            $return = false;
            
            print $prompt;
            
            if (($fh = fopen('php://stdin', 'r'))) {
                $return = trim(fgets($fh));
                fclose($fh);
            }
            
            return $return;
        }
    }
}
