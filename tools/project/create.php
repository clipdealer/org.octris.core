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

// initialization
use \org\octris\core\app\cli as cli;
use \org\octris\core\config as config;

$cfg = new config('org.octris.core');
$prj = new config('org.octris.core', 'project.create');

print "\noctris -- create new project\n";
cli::hline(); 

$prj->defaults(array(
    'info.company' => '',
    'info.author'  => '',
    'info.email'   => '',
    'info.domain'  => ''
));

// collect information and create configuration for new project
$prompt = new cli\readline();

$filter = $prj->filter('info');

foreach ($filter as $k => $v) {
    $prj[$k] = $prompt->get(sprintf("%s [%%s]: ", $k), $v);
}

$prj->save();

$module = $prompt->get("\nmodule [%s]: ");

$ns = '\\' . implode('\\', array_reverse(explode('.', $prj['info.domain']))) . '\\' . $module;

$data = array_merge($prj->filter('info')->getArrayCopy(true), array(
    'module'    => $module,
    'namespace' => $ns,
    'directory' => str_replace('\\', '.', ltrim($ns, '\\'))
));

print_r($data);
