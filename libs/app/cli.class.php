<?php

namespace org\octris\core\app {
    use \org\octris\core\validate as validate;
    
    require_once('org.octris.core/app.class.php');

    /****c* app/cli
     * NAME
     *      cli
     * FUNCTION
     *      core class for CLI applications
     * COPYRIGHT
     *      copyright (c) 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */

    class cli extends \org\octris\core\app {
        /****m* cli/process
         * SYNOPSIS
         */
        public function process()
        /*
         * FUNCTION
         *      Main application processor. This is the only method that needs to
         *      be called to invoke an application. Internally this method determines
         *      the last visited page and handles everything required to determine
         *      the next page to display.
         * EXAMPLE
         *      simple example to invoke an application, assuming that "test" implements
         *      an application base on lima_apps
         *
         *      ..  source: php
         *
         *          $app = new test();
         *          $app->process();
         ****
         */
        {
            $last_page = $this->getLastPage();
            $action    = $last_page->getAction();
            $last_page->validate($this, $action);

            $next_page = $last_page->getNextPage($this, $this->entry_page);

            $max = 3;

            do {
                $redirect_page = $next_page->prepareRender($this, $last_page, $action);

                if (is_object($redirect_page) && $next_page != $redirect_page) {
                    $next_page = $redirect_page;
                } else {
                    break;
                }
            } while (--$max);

            // process with page
            $this->setLastPage($next_page);

            $next_page->prepareMessages($this);
            $next_page->render($this);
        }
        
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
            $key  = '';

            foreach ($args as $arg) {
                print "$arg\n";
                
                if (preg_match('/^-([a-zA-Z]+)$/', $arg, $match)) {
                    // short option, combined short options
                    $tmp  = str_split($match[1], 1);
                    $opts = array_merge(array_combine($tmp, array_fill(0, count($tmp), true)), $opts);
                    $key  = array_pop($tmp);
                    
                    continue;
                } elseif (preg_match('/^--([a-zA-Z][a-zA-Z0-9]+)(=.*|)$/', $arg, $match)) {
                    // long option
                    $key  = $match[1];
                    $opts = array_merge(array($key => true), $opts);

                    if (strlen($match[2]) != 0) {
                        continue;
                    }

                    $arg = substr($match[2], 1);
                } elseif (substr($arg, 0, 1) == '-') {
                    // invalid option format
                    throw new \Exception('invalid option format "' . $arg . '"');
                }

                if ($key == '') {
                    // unknown option
                    throw new \Exception('invalid option format "' . $arg . '"');
                } else {
                    if (!is_bool($opts[$key])) {
                        // multiple values for this option
                        if (!is_array($opts[$key])) {
                            $opts[$key] = array($opts[$key]);
                        }
                        
                        $opts[$key][] = $arg;
                    } else {
                        $opts[$key] = $arg;
                    }
                }
            }

            return $opts;
        }
    }

    if (!defined('OCTRIS_WRAPPER')) {
        // enable validation for superglobals
        define('OCTRIS_WRAPPER', true);
        
        $_SERVER  = new validate\wrapper($_SERVER);
        $_ENV     = new validate\wrapper($_ENV);
        $_GET     = new validate\wrapper(cli::getOptions());
            
        unset($_POST);
        unset($_REQUEST);
        unset($_COOKIE);
        unset($_SESSION);
        unset($_FILES);
    }
}

