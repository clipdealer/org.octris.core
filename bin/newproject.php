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

$data = array(
    'company'   => 'ClipDealer GmbH',
    'author'    => 'Harald Lapp',
    'email'     => 'h.lapp@clipdealer.de',
    'year'      => date('Y'),
    'domain'    => 'clipdealer.de'
);

// $data = array(
//     'domain'    => 'org.octris',
//     'module'    => 'skel'
// );
// 
// $data['project']   = sprintf('%s.%s', $data['domain'], $data['module']);
// $data['namespace'] = str_replace('.', '\\', $data['project']);

$info = posix_getpwuid(posix_getuid());

$yaml = yaml_emit($data);

print_r($info);
print_r($yaml);