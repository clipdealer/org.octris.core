<?php

namespace org\octris\core\project\app {
    /**
     * Main application class for project tools.
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
        protected $entry_page = '\org\octris\core\project\app\entry';
        /**/
    }
}
