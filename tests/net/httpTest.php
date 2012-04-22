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

class httpTest extends PHPUnit_Framework_TestCase {
    public function testFetch() {
        $curl = new \org\octris\core\net\client\http(new \org\octris\core\type\uri('http://www.example.org/'));
        $result = $curl->execute();

        // print_r($result);
        // die;
    }

    public function testMultiFetch() {
        $net = new \org\octris\core\net(2);

        for ($i = 0; $i < 10; ++$i) {
            $net->addClient(new \org\octris\core\net\client\http(new \org\octris\core\type\uri('http://www.example.org/')));
        }

        $net->execute();

        die;
    }
}
