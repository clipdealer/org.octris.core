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
         * Last page
         *
         * @octdoc  v:cli/$last_pages
         * @var     \org\octris\core\cli\page
         */
        private $last_pages = null;
        /**/
        
        /**
         * Mapping of an option to an application page class.
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
                if ($_GET[$option]->isSet) {
                    $instance = new $class();
                    
                    $instance->prepare();
                    $instance->render();
                }
            }

            // handle page flow
            $action    = 'default';
            $parameter = array();
            
            do {
                // determine next page to display
                $last_page = $this->getLastPage();
                $next_page = $last_page->getNextPage($action, $this->entry_page);
                
                // parameter validation for next page
                $redirect_page = $next_page->validate(
                    $last_page,
                    $action,
                    (is_array($parameter) ? $parameter : array())
                );
                
                if (is_object($redirect_page) && $next_page != $redirect_page) {
                    $next_page = $last_page;
                }
                
                // perform possible redirects
                $max = 3;
                
                do {
                    $redirect_page = $next_page->prepare($last_page, $action);
                    
                    if (is_object($redirect_page) && $next_page != $redirect_page) {
                        $next_page = $redirect_page;
                    } else {
                        break;
                    }
                } while (--$max);
                
                // perform next page
                $this->setLastPage($next_page);
                
                $next_page->showErrors();
                $next_page->showMessages();

                list($action, $parameter) = $next_page->dialog($action);
                
                $action = ($action != '' ? $action : 'default');
            } while (true);
        }
        
        /**
         * Try to determine the last visited page stored in the last pages stack. If the
         * last visited page can't be determined (eg.: when entering the application),
         * a new instance of the applications' entry page is created.
         *
         * @octdoc  m:cli/getLastPage
         * @return  \org\octris\core\app\page           Returns instance of determined last visit page or instance of entry page.
         */
        protected function getLastPage()
        /**/
        {
            if (($last_page = $this->last_page)) {
                $this->last_page = null;
            } else {
                $last_page = new $this->entry_page();
            }
            
            return $last_page;
        }

        /**
         * Make a page the last visited page. This method is called internally by the 'process' method
         * before aquiring an other application page.
         *
         * @octdoc  m:cli/setLastPage
         * @param   \org\octris\core\app\page       $page           Page object to set as last visited page.
         */
        protected function setLastPage(\org\octris\core\app\page $page)
        /**/
        {
            $this->last_page = $page;
        }

        /**
         * Determine the action the page was called with.
         *
         * @octdoc  m:cli/getAction
         * @return  string                                  Name of action.
         */
        public function getAction()
        /**/
        {
            if (isset($this->state['ACTION'])) {
                $action = $this->state['ACTION'];
            }

            return ($action == '' ? 'default' : $action);
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
        public static function getOptions()
        /**/
        {
            global $argv;
            static $opts = null;
            
            if (is_array($opts)) {
                // already parsed
                return $opts;
            }

            $args = $argv;
            $opts = array();
            $key  = '';
            $idx  = 1;

            array_shift($args);

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
        $_REQUEST = new validate\wrapper(cli::getOptions());
            
        unset($_POST);
        unset($_GET);
        unset($_COOKIE);
        unset($_SESSION);
        unset($_FILES);
        
        if (!$_ENV['OCTRIS_BASE']->validate(array('type' => validate::T_PATH))) {
            die("OCTRIS_BASE is not set\n");
        }
    }
}

