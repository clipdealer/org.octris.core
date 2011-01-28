<?php

namespace org\octris\core\app {
    use \org\octris\core\validate as validate;
    
    require_once('org.octris.core/app.class.php');
    require_once('org.octris.core/app/cli/autoloader.class.php');

    /**
     * Core class for CLI applications.
     *
     * @octdoc      c:app/cli
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class cli extends \org\octris\core\app
    /**/
    {
        /**
         * Mapping of an option to an application class
         *
         * @octdoc  v:cli/$option_map
         * @var     array
         */
        protected $option_map = array();
        /**/
        
        /**
         * Initialization of cli application.
         *
         * @octdoc  m:cli/initialization
         */
        protected function initialize()
        /**/
        {
            $this->state = new \org\octris\core\app\state();
        }
        
        /**
         * Main application processor. This is the only method that needs to be called to
         * invoke an application. Internally this method determines the last visited page
         * and handles everything required to determine the next page to display.
         *
         * The following example shows how to invoke an application, assuming that 'test'
         * implements an application based on \org\octris\core\app.
         *
         * <code>
         * $app = test::getInstance();
         * $app->process();
         * </code>
         *
         * @octdoc  m:cli/process
         */
        public function process()
        /**/
        {
            // handle command line options
            foreach ($this->option_map as $option => $class) {
                if (isset($_GET[$option])) {
                    $instance = new $class();
                    $instance->prepare();
                }
            }
        }
        
        /**
         * Parse command line options and return Array of them. The parameters are required to have
         * the following format:
         *
         * - short options: -l -a -b
         * - short options combined: -lab
         * - short options with value: -l val -a val -b "with whitespace"
         * - long options: --option1 --option2
         * - long options with value: --option=value --option value --option "with whitespace"
         *
         * @octdoc  m:cli/getOptions
         * @return  array                               Parsed command line parameters.
         */
        public function getOptions()
        /**/
        {
            global $argv;
            static $opts = null;
            
            if (is_array($opts)) {
                // already parsed
                return $opts;
            }

            $args = $argv;
            $opts = array(array_shift($args));
            $key  = '';
            $idx  = 1;

            foreach ($args as $arg) {
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

                    if (strlen($match[2]) == 0) {
                        continue;
                    }

                    $arg = substr($match[2], 1);
                } elseif (strlen($arg) > 1 && substr($arg, 0, 1) == '-') {
                    // invalid option format
                    throw new \Exception('invalid option format "' . $arg . '"');
                }

                if ($key == '') {
                    // no option name, add as numeric option
                    $opts[$idx++] = $arg;
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
        
        if (!$_ENV->validate('OCTRIS_BASE', validate::T_PATH)) {
            die("OCTRIS_BASE is not set\n");
        }
    }
}

