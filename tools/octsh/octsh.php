#!/usr/bin/env php
<?php

/**
 * Octris framework shell.
 *
 * @octdoc      h:tools/octsh
 * @copyright   copyright (c) 2011 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
/**/

require_once('org.octris.core/app/cli.class.php');
// require_once(__DIR__ . '/libs/plugin.class.php');

print "pre";

use \org\octris\core\tools\octsh\libs\main as main;

main::test();
print "OK!";
die;

$info    = posix_getpwuid(posix_getuid());
$history = $info['dir'] . '/.octris/octsh_history';

$prompt   = 'octsh> ';
$readline = \org\octris\core\app\cli\readline::getInstance($history);


do {
    $return = $readline->readline($prompt);
    
    if ($return == 'quit') break;
} while(true);
