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
    
    public function setUp() {
        $this->cache = new \org\octris\core\cache\storage\apc();
    }

    public function testSave() {
        $value = uniqid();

        $this->cache->save('test1', $value);
        $this->assertEquals(apc_fetch('test1'), $value);
    }
}
