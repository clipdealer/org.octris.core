<?php

$_POST = array('p1' => 1, 'p2' => 2);

require_once('org.octris.core/app.class.php');
require_once('org.octris.core/validate/wrapper.class.php');

$v = new \org\octris\core\validate\schema(array(
    'default' => array(                        // entry point, always required!
        'type'          => 'object',
        'properties'    => array(
            'p1' => array(
                'type'  => 'digit'
            ),
            'p2' => array(
                'type'  => 'digit'
            )
        )
    )
), 'array');
$r = (int)$v->validate($_POST);

foreach ($_POST as $k => $v) {
    print "$k";
    print_r($v);
}

print (int)($_POST instanceof Traversable);
// print $r;

?>