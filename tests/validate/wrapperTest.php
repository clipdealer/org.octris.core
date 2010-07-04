<?php

require_once('org.octris.core/app/test.class.php');

use \org\octris\core\app\test as test;

class wrapperTest extends PHPUnit_Framework_TestCase {
    function testWrapper() {
        return new \org\octris\core\validate\wrapper(array(
            'test' => 'test'
        ));
    }

    /**
     * @depends testWrapper
     */
    function testIsTainted($data) {
        $this->assertTrue($data['test']->isTainted);
    }
    
    /**
     * @depends testWrapper
     */
    function testValueIsEmpty($data) {
        $this->assertEquals('', $data['test']->value);
    }
    
    /**
     * @depends testWrapper
     */
    function testTaintedIsNotEmpty($data) {
        $this->assertEquals('test', $data['test']->tainted);
    }
    
    /**
     * @depends testWrapper
     */
    function testIsValid($data) {
        $data->validate('test', \org\octris\core\validate::T_ALPHA);
        $this->assertTrue($data['test']->isValid);
        
        return $data;
    }
    
    /**
     * @depends testIsValid
     */
    function testValueIsNotEmpty($data) {
        $this->assertEquals('test', $data['test']->value);
    }
}
