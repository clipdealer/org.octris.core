<?php

require_once('org.octris.core/app/test.class.php');

use \org\octris\core\app\test as test;

class collectionTest extends PHPUnit_Framework_TestCase {
    function dataProvider() {
        // return array(
        //     new \org\octris\core\type\collection(array(1, 2, 3)),
        // return new \org\octris\core\type\collection(array(
        //     'eins' => 1, 'zwei', 2, 3
        // ));
        // );
    }
    
    function assocCollection() {
    }

    /**
     * @depends testCollection
     */
    function testGetArrayCopy($data) {
        $this->assertEquals($data->getArrayCopy(), array(1, 2, 3));
    }
    
    /**
     * @depends testCollection
     */
    function testCount($data) {
        $this->assertEquals(count($data), 3);
    }
    
    /**
     * @depends testCollection
     */
    function testMege($data) {
        $data->merge(array(4, 5, 6), array(7, 8, 9));
        
        $this->assertEquals($data->getArrayCopy(), array(1, 2, 3, 4, 5, 6, 7, 8, 9));
    }
}
