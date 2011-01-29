<?php

namespace org\octris\core\octsh\app {
    /**
     * Clear the shell screen.
     *
     * @octdoc      c:app/clear
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class clear extends \org\octris\core\octsh\libs\plugin
    /**/
    {
        /**
         * The entry points to which the current page should allow requests to have to be defined with this
         * property.
         *
         * @octdoc  v:clear/$next_page
         * @var     array
         */
        protected $next_pages = array();
        /**/

        /**
         * Prepare page
         *
         * @octdoc  m:clear/prepare
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
         * @octdoc  m:clear/dialog
         * @param   string                          $action         Action that led to current page.
         */
        public function dialog($action)
        /**/
        {
            \org\octris\core\app\cli\stdio::clear();
        }
    }
}
