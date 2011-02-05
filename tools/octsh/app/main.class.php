<?php

namespace org\octris\core\octsh\app {
    use \org\octris\core\validate as validate;
    use \org\octris\core\provider as provider;
    
    /**
     * Main application class for octris shell.
     *
     * @octdoc      c:app/main
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class main extends \org\octris\core\app\cli
    /**/
    {
        /**
         * Entry page to use if no other page is loaded. To be overwritten by applications' main class.
         *
         * @octdoc  v:main/$entry_page
         * @var     string
         */
        protected $entry_page = '\org\octris\core\octsh\app\entry';
        /**/

        /**
         * Initialization of shell.
         *
         * @octdoc  m:main/initialization
         */
        protected function initialize()
        /**/
        {
            parent::initialize();
            
            $env = provider::access('env');
            
            $this->state['project'] = $env->getValue('OCTRIS_APP', validate::T_PROJECT);
        }
        
        /**
         * Overwrite process method of cli application controller to provide a nice header message
         * when starting shell.
         *
         * @octdoc  m:main/process
         */
        public function process()
        /**/
        {
            print "Welcome to the OCTRiS shell.\n";
            print "enter 'quit' to exit the shell or 'help' to get any further usage help.\n\n";
            
            parent::process();
        }
    }
}
