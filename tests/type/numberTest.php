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

class numberTest extends PHPUnit_Framework_TestCase {
    public function testPrecision() {
        $num = new \org\octris\core\type\number();

        // 0.1 + 0.2 - 0.3 : -0 (PHP: 5.5511151231258E-17)
        $this->assertEquals($num->set(0.1)->add(0.2)->sub(0.3)->get(), '0.0');
    }

    public function testCeil() {
        $num = new \org\octris\core\type\number();

        $tests = array('4.3', '9.999', '-3.14');

        foreach ($tests as $test) {
            $this->assertEquals($num->set($test)->ceil()->get(), ceil($test));
        }
    }

    public function testFloor() {
        $num = new \org\octris\core\type\number();

        $tests = array('4.3', '9.999', '-3.14');

        foreach ($tests as $test) {
            $this->assertEquals($num->set($test)->floor()->get(), floor($test));
        }
    }

    public function testRound() {
        $num = new \org\octris\core\type\number();

        $tests = array(
            '3.4' => 0, '3.5' => 0, '3.6' => 0,
            '1.95583' => 2, '5.045' => 2, '5.055' => 2, '9.999' => 2
        );

        foreach ($tests as $val => $prec) {
            $this->assertEquals($num->set($val)->round($prec)->get(), round($val, $prec));
        }
    }
}
