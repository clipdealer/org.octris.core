<?php

namespace {{$namespace}} {
    /****c* host/index
     * NAME
     *      index
     * FUNCTION
     *      application loader
     * COPYRIGHT
     *      copyright (c) {{$year}} by {{$company}}
     * AUTHOR
     *      {{$author}} <{{$email}}>
     ****
     */

    // include core web application library
    require_once('org.octris.core/app/web.class.php');
    
    // load application configuration
    \org\octris\core\config::load('{{$directory}}');

    // run application
    app\main::getInstance()->process();
}
