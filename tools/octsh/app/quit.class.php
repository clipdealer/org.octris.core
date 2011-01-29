<?php

namespace org\octris\core\octsh\app {
    /**
     * Quit the shell.
     *
     * @octdoc      c:app/quit
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class quit extends \org\octris\core\octsh\libs\plugin
    /**/
    {
        /**
         * The entry points to which the current page should allow requests to have to be defined with this
         * property.
         *
         * @octdoc  v:quit/$next_page
         * @var     array
         */
        protected $next_pages = array();
        /**/

        /**
         * Prepare page
         *
         * @octdoc  m:quit/prepare
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
         * @octdoc  m:quit/dialog
         * @param   string                          $action         Action that led to current page.
         */
        public function dialog($action)
        /**/
        {
            print "Good bye!\n";
            exit(0);
        }
    }
}
