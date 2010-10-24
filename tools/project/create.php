#!/usr/bin/env php
<?php

/****h* project/create
 * NAME
 *      create.php
 * FUNCTION
 *      creates a new project from project skeleton
 * COPYRIGHT
 *      copyright (c) 2010 by Harald Lapp
 * AUTHOR
 *      Harald Lapp <harald@octris.org>
 ****
 */

require_once(__DIR__ . '/../../libs/app/cli.class.php');

\org\octris\core\config::load('org.octris.core');

use \org\octris\core\app\cli as cli;

$data = array(
    'company' => '',
    'author'  => '',
    'email'   => '',
    'domain'  => ''
);

print "octris -- create new project\n";
cli::hline(); 

$prompt = new cli\readline();

foreach ($data as $k => $v) {
    $input = $prompt->get(sprintf("%s [%s]: ", $k, $v));
}

$data = array_merge($data, array(
    'module'    => '',
    'namespace' => '\\' . implode('\\', array_reverse(explode('.', $data['domain'])))
));

print_r($data);
