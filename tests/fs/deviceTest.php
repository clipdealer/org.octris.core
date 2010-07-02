<?php

require_once('org.octris.core/app/test.class.php');

use \org\octris\core\app\test as test;

class deviceTest extends PHPUnit_Framework_TestCase {
    public function testParseMode() {
        $class = '\org\octris\core\fs\device';

        $cases = array(
            'r'     => $class::T_READ,
            'rb'    => $class::T_READ | $class::T_BINARY,
            'r+'    => $class::T_READ | $class::T_WRITE,
            'rb+'   => $class::T_READ | $class::T_WRITE | $class::T_BINARY,
            'w'     => $class::T_WRITE,
            'w+'    => $class::T_WRITE | $class::T_READ,
            'wb+'   => $class::T_WRITE | $class::T_READ | $class::T_BINARY,
            'a'     => $class::T_APPEND | $class::T_READ,
            'a+'    => $class::T_APPEND | $class::T_READ | $class::T_WRITE,
            'ab+'   => $class::T_APPEND | $class::T_READ | $class::T_WRITE | $class::T_BINARY
        );
        
        $stub = $this->getMockForAbstractClass($class);

        foreach ($cases as $k => $v) {
            $method = test::getMethod($class, 'parseMode');
            $method->invokeArgs($stub, array($k));
        
            $property = test::getProperty($class, 'mode');
            $this->assertEquals($v, $property->getValue($stub), '"' . $k . '" failed!');
        }
    }
}
