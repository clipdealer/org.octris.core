<?php

namespace {{$namespace}}\app {
    /**
     * Main application class. This class is only used to define an entry page - if it's the
     * first request to the web application and therefore no other page (next_page) is specified through the 
     * application state, this entry page is required.
     *
     * @octdoc      c:app/main
     * @copyright   copyright (c) {{$year}} by {{$company}}
     * @author      {{$author}} <{{$email}}>
     */
    class main extends \org\octris\core\app
    /**/
    {
        /**
         * Page to use a entry point, if no "next_page" is specified through the
         * application state.
         *
         * @octdoc  v:main/$entry_page
         * @var     string
         */
        protected $entry_page = '{{$namespace}}\app\entry';
        /**/
    }
}
