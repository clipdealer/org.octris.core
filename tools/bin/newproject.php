#!/usr/bin/env php
<?php

/****h* bin/newproject
 * NAME
 *      newproject.php
 * FUNCTION
 *      creates a new project from project skeleton
 * COPYRIGHT
 *      copyright (c) 2010 by Harald Lapp
 * AUTHOR
 *      Harald Lapp <harald@octris.org>
 ****
 */

require_once(__DIR__ . '/../libs/app/cli.class.php');

\org\octris\core\config::load('org.octris.core');


$data = array(
    'company' => '',
    'author'  => '',
    'email'   => '',
    'domain'  => ''
);

print "octris -- create new project\n";
print "=====================================================================\n";

$fp = fopen('php://stdin', 'r');

foreach ($data as $k => $v) {
    printf("%s [%s]: ", $k, $v);
    
    $input = fgets($fp, 50);
}

fclose($fp);

$data = array_merge($data, array(
    'module'    => '',
    'namespace' => '\\' . implode('\\', array_reverse(explode('.', $data['domain'])))
));

print config::
print_r($data);
