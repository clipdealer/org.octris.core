<?php

require_once('org.octris.core/app/test.class.php');

use \org\octris\core\app\test as test;

class schemaTest extends PHPUnit_Framework_TestCase {
    public function testRequest() {
        $data = array('p1' => 1, 'p2' => 2);

        $wrapper = new \org\octris\core\validate\wrapper($data);
        $schema  = new \org\octris\core\validate\schema(array(
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
        
        $r = (int)$schema->validate($wrapper);
        
        print "p1->isSet: " . (int)$wrapper['p1']->isSet . "\n";
        print "p1->validate: " . (int)$wrapper['p1']->validate('digit') . "\n";
        
        print_r($wrapper);
    }
}
