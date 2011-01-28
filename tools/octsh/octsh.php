#!/usr/bin/env php
<?php

namespace org\octris\core\octsh {
    /**
     * Octris framework shell.
     *
     * @octdoc      h:tools/octsh
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    /**/

    $_ENV['OCTRIS_APP'] = 'org.octris.core';
    $_ENV['OCTRIS_BASE'] = '/Users/harald/Projects';

    // include core cli application library
    require_once('org.octris.core/app/cli.class.php');
    
    // load application configuration
    // $registry = \org\octris\core\registry::getInstance();
    // $registry->set('config', function() {
    //     return new \org\octris\core\config('{{$directory}}');
    // }, \org\octris\core\registry::T_SHARED | \org\octris\core\registry::T_READONLY);

    // run application
    app\main::getInstance()->process();
    
    // $info    = posix_getpwuid(posix_getuid());
    // $history = $info['dir'] . '/.octris/octsh_history';
    // 
    // $prompt   = 'octsh> ';
    // $readline = \org\octris\core\app\cli\readline::getInstance($history);
    // 
    // 
    // do {
    //     $return = $readline->readline($prompt);
    //     
    //     if ($return == 'quit') break;
    // } while(true);
}
