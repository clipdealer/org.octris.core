<?php

require_once('org.octris.core/app/test.class.php');

use \org\octris\core\app\test as test;

class schemaTest extends PHPUnit_Framework_TestCase {
    public function testSchema() {
        // $_POST = array('p1' => 1, 'p2' => 2);
        // 
        // $v = new \org\octris\core\validate\schema(array(
        //     'default' => array(                        // entry point, always required!
        //         'type'          => 'object',
        //         'properties'    => array(
        //             'p1' => array(
        //                 'type'  => 'digit'
        //             ),
        //             'p2' => array(
        //                 'type'  => 'digit'
        //             )
        //         )
        //     )
        // ), 'array');
        // // $r = (int)$v->validate($_POST);
        // 
        // print "p1->isSet: " . (int)$_POST['p1']->isSet . "\n";
        // print "p1->validate: " . (int)$_POST['p1']->validate('digit') . "\n";
        // 
        // foreach ($_POST as $k => $v) {
        //     print "$k";
        //     print_r($v);
        // }
    }
}
