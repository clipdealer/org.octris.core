<?php

require_once('org.octris.core/app/test.class.php');

use \org\octris\core\app\test as test;

class cliTest extends PHPUnit_Framework_TestCase {
    public function testGetOptions() {
        $GLOBALS['argv'] = array(
            'test', '--option1', '--option2=test', '--option3=test test',
            '-o', '-p', 'option5', '-q', 'option6 test', '-abc',
            '--option8', 'test', '--option9', 'with whitespace'
        );

        $test = array(
          'option1' => true, 'option2' => 'test', 'option3' => 'test test',
          'o' => true, 'p' => 'option5', 'q' => 'option6 test',
          'a' => true, 'b' => true, 'c' => true,
          'option8' => 'test', 'option9' => 'with whitespace',
        );

        $options = \org\octris\core\app\cli::getOptions();

        $this->assertEquals($options, $test);
    }
}
