<?php

require_once('org.octris.core/app/autoloader.class.php');

$tpl = new \org\octris\core\tpl();
$tpl->addSearchPath(dirname(__FILE__) . '/../../tests/tpl/compiler/');
$tpl->render('tpl1.html');

