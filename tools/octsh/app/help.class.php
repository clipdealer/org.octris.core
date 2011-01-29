<?php

namespace org\octris\core\octsh\app {
    /**
     * Help system for the shell.
     *
     * @octdoc      c:app/help
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class help extends \org\octris\core\octsh\libs\plugin
    /**/
    {
        /**
         * The entry points to which the current page should allow requests to have to be defined with this
         * property.
         *
         * @octdoc  v:help/$next_page
         * @var     array
         */
        protected $next_pages = array();
        /**/

        /**
         * Prepare page
         *
         * @octdoc  m:help/prepare
         * @param   \org\octris\core\app\page       $last_page      Instance of last called page.
         * @param   string                          $action         Action that led to current page.
         * @return  mixed                                           Returns either page to redirect to or null.
         */
        public function prepare(\org\octris\core\app\page $last_page, $action)
        /**/
        {
        }

        /**
         * Implements dialog.
         *
         * @octdoc  m:help/dialog
         * @param   string                          $action         Action that led to current page.
         */
        public function dialog($action)
        /**/
        {
            $registry = \org\octris\core\registry::getInstance();
            
            if (!isset($registry->commands)) {
                print "ERROR: help is not available\n";
            } else {
                print "List of all OCTRiS shell commands:\n\n";
                
                foreach ($registry->commands as $command => $class) {
                    printf("    %s\n", $command);
                }
                
                print "\n";
                print "Enter 'help <command>' for more information on a specific command.\n";
            }
        }
    }
}
