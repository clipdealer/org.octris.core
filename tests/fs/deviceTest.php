<?php

require_once('org.octris.core/app/test.class.php');

use \org\octris\core\app\test as test;

class deviceTest extends PHPUnit_Framework_TestCase {
    public function testParseMode() {
        $method = test::getMethod('\org\octris\core\fs\device', 'parseMode');
        
        print_r($method);
    }
}