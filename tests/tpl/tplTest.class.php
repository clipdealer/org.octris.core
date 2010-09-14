<?php

require_once('org.octris.core/app/autoloader.class.php');

class l10n {
    function _($msg) {
        return $msg;
    }
    function gettext($msg) {
        return $msg;
    }
    function lookup($msg) {
        return $msg;
    }
}

$l10n = new l10n();

$tpl = new \org\octris\core\tpl();
$tpl->setL10n($l10n);
$tpl->addSearchPath(dirname(__FILE__) . '/../../tests/tpl/compiler/');
print $tpl->compile('tpl1.html');

