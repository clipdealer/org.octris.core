<?php

require_once('org.octris.core/app/autoloader.class.php');

$tpl = new \org\octris\core\tpl();
$tpl->registerMethod('gettext', function($msg) {
    print "[$msg]\n";
    
    return $msg;
}, array('min' => 1, 'max' => 1));
$tpl->addSearchPath(dirname(__FILE__) . '/../../tests/tpl/compiler/');
print $tpl->compile('tpl1.html');

