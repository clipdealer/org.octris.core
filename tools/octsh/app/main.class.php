<?php

namespace org\octris\core\octsh\app {
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
         * Mapping of an option to an application class
         *
         * @octdoc  v:main/$option_map
         * @var     
         */
        protected $option_map = array(
            '--help'    => '\org\octris\core\octsh\app\clihelp'
        );
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
            
            $this->state['project'] = $_ENV['OCTRIS_APP'];
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
