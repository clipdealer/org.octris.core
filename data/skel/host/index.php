<?php

namespace {{$SKEL_NAMESPACE}} {
    /****c* host/index
     * NAME
     *      index
     * FUNCTION
     *      application loader
     * COPYRIGHT
     *      copyright (c) {{$SKEL_YEAR}} by {{$SKEL_COMPANY}}
     * AUTHOR
     *      {{$SKEL_AUTHOR}} <{{$SKEL_EMAIL}}>
     ****
     */

    // include core web application library
    require_once('org.octris.core/app/web.class.php');
    
    // load application configuration
    \org\octris\core\config::load('{{$SKEL}}');

    // run application
    app\main::getInstance()->process();
}
