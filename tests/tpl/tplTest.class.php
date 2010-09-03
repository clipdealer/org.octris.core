<?php

require_once('org.octris.core/app/autoloader.class.php');

$test = new \org\octris\core\tpl\compiler();
$tpl  = $test->process(dirname(__FILE__) . '/../../tests/tpl/compiler/tpl1.html');

print "\n\n$tpl\n\n";

// TEST
class test extends \org\octris\core\tpl\sandbox {
    function run($file) {
        require_once($file);
    }
}

$file = tempnam('/tmp', 'php');
file_put_contents($file, $tpl);

$s = new test();
$s->setValue('data', array('eins', 'zwei', 'drei', '<vier>', '</vier>'));
$s->setValue('import', true);

$s->setValue('rec', array(array(1,2,3),array(4,5,6),array(7,8,9)));

$s->run($file);

unlink($file);
