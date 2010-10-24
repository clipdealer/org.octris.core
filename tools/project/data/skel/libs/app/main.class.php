<?php

namespace {{$SKEL_NAMESPACE}}\app {
    /****c* app/main
     * NAME
     *      main
     * FUNCTION
     *      main application. this class is only used to define an entry page - if it's the
     *      first request to the web application and therefore no other page (next_page) 
     *      is specified through the application state, this entry page is required.
     * COPYRIGHT
     *      copyright (c) {{$SKEL_YEAR}} by {{$SKEL_COMPANY}}
     * AUTHOR
     *      {{$SKEL_AUTHOR}} <{{$SKEL_EMAIL}}>
     ****
     */

    class main extends \org\octris\core\app {
        /****v* main/$entry_page
         * SYNOPSIS
         */
        protected $entry_page = 'entry';
        /*
         * FUNCTION
         *      page to use a entry point, if no "next_page" is specified through the
         *      application state.
         ****
         */
    }
}
