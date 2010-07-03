<?php

require_once('org.octris.core/app/test.class.php');

use \org\octris\core\app\test as test;

class stateTest extends PHPUnit_Framework_TestCase {
    protected $state;
    
    public function setUp() {
        $this->state = new \org\octris\core\app\state();
    }
    
    public function testThaw() {
        $state = new \org\octris\core\app\state();
        $state->push('test', 'test');
        
        $secret = 'origami';
        $frozen = $state->freeze($secret);
        
        $thawed = \org\octris\core\app\state::thaw($frozen, $secret);
        
        $this->assertEquals($state, $thawed);
    }
}

