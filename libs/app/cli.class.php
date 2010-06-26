<?php

require_once('org.octris.core/app.class.php');

namespace org\octris\core\app {
    class cli extends \org\octris\core\app {
        /****m* cli/getOptions
         * SYNOPSIS
         */
        static function getOptions()
        /*
         * FUNCTION
         *      parse command line options and return array of it. The
         *      parameters are required to have the following format:
         *
         *      *   short options: -l -a -b
         *      *   short options combined: -lab
         *      *   short options with value: -l val -a val -b "with whitespace"
         *      *   long options: --option1 --option2
         *      *   long options with value: --option=value --option value --option "with whitespace"
         * OUTPUTS
         *      (array) -- parsed command line parameters
         ****
         */
        {
            global $argv;
            static $opts = null;
            
            if (is_array($opts)) {
                // already parsed
                return $opts;
            }

            $args = $argv;

            array_shift($args);
            $opts = array();
            $key = '';

            foreach ($args as $arg) {
                if (preg_match('/^--?[^-]/', $arg) && $key != '') {
                    $opts[$key] = true;
                    $key = '';
                }
                
                if (preg_match('/^-([a-zA-Z]+)$/', $arg, $match)) {
                    // short option, combined short options
                    if (strlen($match[1]) > 1) {
                        $tmp  = str_split($match[1], 1);
                        $opts = array_merge($opts, array_combine($tmp, array_fill(0, count($tmp), true)));
                    } else {
                        $key = $match[1];
                    }
                    
                    continue;
                } elseif (preg_match('/^--([a-zA-Z][a-zA-Z0-9]+)(=.*|)$/', $arg, $match)) {
                    // long option
                    $key = $match[1];
                    
                    if (strlen($match[2]) == 0) {
                        continue;
                    }
                    
                    $arg = substr($match[2], 1);
                } elseif (substr($arg, 0, 1) == '-') {
                    throw new Exception('wrong parameter format "' . $arg . '"');
                }
                
                if ($key == '') {
                    throw new Exception('wrong parameter format "' . $arg . '"');
                } else {
                    $opts[$key] = $arg;
                    $key        = '';
                }
            }

            return $opts;
        }
    }

    // enable validation for superglobals
    $_SERVER  = new \org\octris\core\validate\wrapper($_SERVER);
    $_ENV     = new \org\octris\core\validate\wrapper($_ENV);
    $_GET     = new \org\octris\core\validate\wrapper(cli::getOptions());
    
    unset($_POST);
    unset($_REQUEST);
    unset($_COOKIE);
}

