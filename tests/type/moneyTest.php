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

class moneyTest extends PHPUnit_Framework_TestCase {
    public function testAllocate() {
        $tests = array(
            array(90, array(70, 30), array(63, 27))
        );

        $mon = new \org\octris\core\type\money();

        foreach ($tests as $test) {
            list($initial, $ratios, $expected) = $test;

            $results = $mon->set($initial)->allocate($ratios);

            for ($i = 0, $cnt = count($results); $i < $cnt; ++$i) {
                $this->assertEquals($results[$i]->get(), $expected[$i]);
            }
        }
    }
}
