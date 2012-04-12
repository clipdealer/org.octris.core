<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once('org.octris.core/app/test.class.php');

use \org\octris\core\app\test as test;

class apcTest extends PHPUnit_Framework_TestCase {
    protected $cache;

    protected $ns = 'org.octris.core.test';
    
    public function setUp() {
        $this->cache = new \org\octris\core\cache\storage\apc(array(
            'ns' => $this->ns
        ));
    }

    public function tearDown() {
        print_r($this->cache);

        $this->cache->clean();
        unset($this->cache);
    }

    /** **/

    public function testSave() {
        $tests = array(
            'scalar' => uniqid(),
            'array'  => array(1, 2, 3, 4),
            'int'    => 1,
        );

        foreach ($tests as $name => $value) {
            $this->cache->save($name, $value);
            $this->assertEquals(apc_fetch($name), $value);
        }
    }

    /**
     * @depends testSave
     */
    public function testInc() {
        $this->cache->inc('int', 2);
        $this->assertEquals(apc_fetch('int', 3));
    }

    /**
     * @depends testInc
     */
    public function testDec() {
        $this->cache->dec('int', 1);
        $this->assertEquals(apc_fetch('int', 2));
    }

    /**
     * @depends testDec
     */
    public function testCas() {
        $this->cache->cas('int', 2, 0);
        $this->assertEquals(apc_fetch('int', 0));
    }
}
