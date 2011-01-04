#!/usr/bin/env php
<?php

/**
 * Tool for creating a new project using a project skeleton.
 *
 * @octdoc      h:project/create
 * @copyright   copyright (c) 2011 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
/**/

require_once(__DIR__ . '/../../libs/app/cli.class.php');

// helper function to check if file is binary
function is_binary($file) {
    $return = false;
    
    if (is_file($file) && ($fp = fopen($file, 'r'))) {
        $blk = fread($fp, 512);
        fclose($fp); 
        
        clearstatcache();
        
        $return = (
            substr_count($blk, '^ -~', "^\r\n") / 512 > 0.3 ||
            substr_count($blk, "\x00") > 0 
        ); 
    }
    
    return $return;
}

// initialization
use \org\octris\core\app\cli as cli;
use \org\octris\core\config as config;
use \org\octris\core\tpl as tpl;

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

print "\n";
$module = $prompt->get('module [%s]: ', 'mod', true);
$year   = $prompt->get('year [%s]: ', date('Y'), true);

if ($module == '' || $year == '') {
    die("'module' and 'year' are required!\n");
}

$ns = '\\' . implode('\\', array_reverse(explode('.', $prj['info.domain']))) . '\\' . $module;

$data = array_merge($prj->filter('info')->getArrayCopy(true), array(
    'year'      => $year,
    'module'    => $module,
    'namespace' => $ns,
    'directory' => str_replace('\\', '.', ltrim($ns, '\\'))
));

// setup destination directory
$dir = cli::getPath(cli::T_PATH_WORK, '') . '/' . $data['directory'];

if (is_dir($dir)) {
    die(sprintf("there seems to be already a project at '%s'\n", $dir));
}

// process skeleton and write project files
$tpl = new tpl();
$tpl->addSearchPath(__DIR__ . '/data/skel/');
$tpl->setValues($data);

$box = new tpl\sandbox();

$src = __DIR__ . '/data/skel/';
$len = strlen($src);

mkdir($dir, 0755);

$directories = array();
$iterator    = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($src)
);

foreach ($iterator as $filename => $cur) {
    $rel  = substr($filename, $len); 
    $dst  = $dir . '/' . $rel;
    $path = dirname($dst);
    $base = basename($filename);
    $ext  = preg_replace('/^\.?[^\.]+?(\..+|)$/', '\1', $base);
    $base = basename($filename, $ext);
    
    if (substr($base, 0, 1) == '$' && isset($data[$base = ltrim($base, '$')])) {
        // resolve variable in filename
        $dst = $path . '/' . $data[$base] . $ext;
    }
    
    if (!is_dir($path)) {
        // create destination directory
        mkdir($path, 0755, true);
    }
    
    if (!is_binary($filename)) {
        $cmp = $tpl->fetch($rel, tpl\sandbox::T_CONTEXT_TEXT);

        file_put_contents($dst, $cmp);
    } else {
        copy($filename, $dst);
    }
}

print "done.\n";
