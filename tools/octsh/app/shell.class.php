<?php

namespace org\octris\core\octsh\app {
    use \org\octris\core\app as app;
    
    /**
     * Implements command line shell.
     *
     * @octdoc      c:app/shell
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class shell extends \org\octris\core\app\cli\page
    /**/
    {
        /**
         * The entry points to which the current page should allow requests to have to be defined with this
         * property.
         *
         * @octdoc  v:shell/$next_page
         * @var     array
         */
        protected $next_pages = array(
            'quit'      => '\org\octris\core\octsh\app\quit',
            'help'      => '\org\octris\core\octsh\app\help',
            'clear'     => '\org\octris\core\octsh\app\clear',
            'error'     => '\org\octris\core\octsh\app\error',
            'install'   => '\org\octris\core\octsh\app\install',
        );
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:shell/__construct
         */
        public function __construct()
        /**/
        {
            parent::__construct();
            
            $registry = \org\octris\core\registry::getInstance();
            
            if (!isset($registry->commands)) {
                // make commands and command classes available through registry
                $commands = $this->next_pages;
                unset($commands['error']);
                ksort($commands);
                
                $registry->set(
                    'commands', 
                    $commands,
                    \org\octris\core\registry::T_SHARED | \org\octris\core\registry::T_READONLY
                );
            }
        }

        /**
         * Prepare page.
         *
         * @octdoc  m:shell/prepare
         * @param   \org\octris\core\app\page       $last_page      Instance of last called page.
         * @param   string                          $action         Action that led to current page.
         * @return  mixed                                           Returns either page to redirect to or null.
         */
        public function prepare(\org\octris\core\app\page $last_page, $action)
        /**/
        {
        }

        /**
         * Render page.
         *
         * @octdoc  m:entry/render
         * @param   string                          $action         Action that led to current page.
         * @return  string                                          Action that was triggered by the dialog.
         */
        public function dialog($action)
        /**/
        {
            if (posix_isatty(STDIN)) {
                // octsh is called from a tty
                $readline = \org\octris\core\app\cli\readline::getInstance('/tmp/octsh.txt');
                $prompt   = 'octsh> ';

                do {
                    $return = trim($readline->readline($prompt));
                } while ($return == '');

                $args = explode(' ', $return);
                $cmd  = array_shift($args);
            } else {
                // octsh is called from a pipe
                if (($cmd = fgets(STDIN))) {
                    $cmd = trim($cmd);
                } else {
                    $cmd = 'quit';
                }
            }

            if (!isset($this->next_pages[$cmd])) {
                $cmd = 'error';
            }

            return $cmd;
        }
    }
}
