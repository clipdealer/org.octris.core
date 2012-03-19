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

class stringTest extends PHPUnit_Framework_TestCase {
    public function testChunk_id() {
    	$tests = array(
    		array(1, 9, 3, '0', '/', '000/000/001/')
    	);

    	foreach ($tests as $test) {
    		list($string, $pad, $chunk_len, $pad_char, $chunk_char, $expected);

    		$this->assertEquals(\org\octris\core\type\string::chunk_id($string, $pad, $chunk_len, $pad_char, $chunk_char), $expected);
    	}
    }
}
