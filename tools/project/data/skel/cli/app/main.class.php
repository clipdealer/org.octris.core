<?php

namespace {{$namespace}}\{{$module}}\app {
    /**
     * Main application class.
     *
     * @octdoc      c:app/main
     * @copyright   copyright (c) {{$year}} by {{$company}}
     * @author      {{$author}} <{{$email}}>
     */
    class main extends \org\octris\core\app\cli
    /**/
    {
        /**
         * Entry page to use if no other page is loaded.
         *
         * @octdoc  v:app/$entry_page
         * @var     string
         */
        protected $entry_page = '\{{$namespace}}\{{$module}}\app\entry';
        /**/

        /**
         * Mapping of an option to an application class
         *
         * @octdoc  v:cli/$option_map
         * @var     array
         */
        protected $option_map = array();
        /**/
    }
}
