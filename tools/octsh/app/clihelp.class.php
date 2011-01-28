<?php

namespace org\octris\core\octsh\app {
    /**
     * Display help page for commandline parameters.
     *
     * @octdoc      c:app/clihelp
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class clihelp extends \org\octris\core\app\cli\page
    /**/
    {
        /**
         * Prepare page
         *
         * @octdoc  m:page/prepare
         * @param   \org\octris\core\app\page       $last_page      Instance of last called page.
         * @param   string                          $action         Action that led to current page.
         * @return  mixed                                           Returns either page to redirect to or null.
         */
        public function prepare(\org\octris\core\app\web\page $last_page, $action)
        /**/
        {
        }

        /**
         * Abstract method definition
         *
         * @octdoc  m:page/render
         * @abstract   
         */
        public function render()
        /**/
        {
            print "help\n";

            exit(0);
        }
    }
}
