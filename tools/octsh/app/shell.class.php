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
            static $line = 0;
            
            // get shell input
            $input = '';
            
            do {
                if (posix_isatty(STDIN)) {
                    // octsh is called from a tty
                    $line = 1;
                    
                    $readline = \org\octris\core\app\cli\readline::getInstance('/tmp/octsh.txt');
                    $prompt   = 'octsh> ';

                    do {
                        $input = trim($readline->readline($prompt));
                    } while ($input == '');

                    $args = explode(' ', $return);
                    $cmd  = array_shift($args);
                } else {
                    // octsh is called from a pipe
                    ++$line;
                    
                    if (($input = fgets(STDIN))) {
                        $input = trim($input);
                    } else {
                        $input = 'quit';
                    }
                }
            } while(substr($input, 0, 1) == '#');

            // process input
            $parser = new \org\octris\core\octsh\libs\parser();
            $result = $parser->parse($input, $line);

            if ($result['error']) {
                // parser error
                $command = 'shell';

                $this->addError(sprintf(
                    'error in line %d at token %s: %s',
                    $result['error']['line'],
                    $result['error']['token'],
                    $result['error']['payload']
                ));
            } else {
                $command   = array_shift($result['command']);
                $parameter = $result['command'];

                if (!isset($this->next_pages[$command])) {
                    $command = 'shell';
                    
                    $this->addError("unknown command '$command'");
                }
            }

            return array($command, $parameter);
        }
    }
}
