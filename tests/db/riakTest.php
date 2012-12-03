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

class riakTest extends PHPUnit_Framework_TestCase {
    protected $db;
    protected $cn;
    
    public function setUp() {
        $this->db = new \org\octris\core\db\device\riak('192.168.178.11', '8098');
        $this->cn = $this->db->getConnection(\org\octris\core\db::T_DB_MASTER);
    }

    public function testIsAlive() {
        $this->assertTrue($this->cn->isAlive());
    }

    public function testGetCollections() {
        $this->cn->getCollections();
    }
}
