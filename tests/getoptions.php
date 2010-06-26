#!/usr/bin/env php
<?php

$argv = array(
    'test', '--option1', '--option2=test', '--option3=test test',
    '-o', '-p', 'option5', '-q', 'option6 test', '-abc',
    '--option8', 'test', '--option9', 'with whitespace'
);

require_once('org.octris.core/app.class.php');
require_once('org.octris.core/app/cli.class.php');

var_dump($_GET);