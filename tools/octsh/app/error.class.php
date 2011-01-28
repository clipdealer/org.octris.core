<?php

namespace org\octris\core\octsh\app {
    /**
     * Error page is called, when an unknown command was entered in the shell.
     *
     * @octdoc      c:app/error
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class error extends \org\octris\core\app\cli\page
    /**/
    {
        /**
         * The entry points to which the current page should allow requests to have to be defined with this
         * property.
         *
         * @octdoc  v:error/$next_page
         * @var     array
         */
        protected $next_pages = array();
        /**/

        /**
         * Prepare page
         *
         * @octdoc  m:error/prepare
         * @param   \org\octris\core\app\page       $last_page      Instance of last called page.
         * @param   string                          $action         Action that led to current page.
         * @return  mixed                                           Returns either page to redirect to or null.
         */
        public function prepare(\org\octris\core\app\page $last_page, $action)
        /**/
        {
        }

        /**
         * Abstract method definition
         *
         * @octdoc  m:error/render
         * @param   string                          $action         Action that led to current page.
         * @return  string                                          empty string.
         */
        public function dialog($action)
        /**/
        {
            print "ERROR: unknown command\n";
            
            return '';
        }
    }
}
