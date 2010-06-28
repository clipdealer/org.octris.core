<?php

require_once('org.octris.core/app.class.php');

class main extends \org\octris\core\app\cli {
}

$i = main::getInstance();

$a = new \org\octris\core\type\collection(array(
    1,
    'string',
    3.5,
    array(
        'a' => '1', 'b' => '2'
    )
));

print count($a) . "\n";

function tree($v) {
    foreach ($v as $k => $i) {
        print "\n$k = ";

        if ($i->item instanceof octris_type_array) {
            tree($i->item);
        } else {
            print_r($i);
        }
    }
}

tree($a);

print "\n\n";

$s = new \org\octris\core\type\string('hallo');
$s = (string)$s->substr(1, 5)->x()->repeat(10);

$a = new \org\octris\core\type\collection($s);

foreach ($a as $i) {
    print "$i->item\n";
}

