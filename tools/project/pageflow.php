#!/usr/bin/env php
<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\project {
    /**
     * Tool for analyzing page flow of a project. Walks from main repective entry page of a project
     * down through each next page and generates a dot diagram, which may be visualized using graphviz.
     *
     * @octdoc      h:project/pageflow
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    /**/

    // include core cli application library
    require_once('org.octris.core/app/cli.class.php');
    
    // check for required parameters and set project to analyze
    $args = \org\octris\core\provider::access('args');
    
    if (!$args->isExist('project') || !($project = $args->getValue('project', \org\octris\core\validate::T_PROJECT))) {
        die("usage: ./project --project=...\n");
    }
    
    $env = \org\octris\core\provider::access('env');
    $env->setValue('OCTRIS_APP', $project, \org\octris\core\validate::T_PROJECT);
    
    // load application configuration
    $registry = \org\octris\core\registry::getInstance();
    $registry->set('config', function() {
        return new \org\octris\core\config('org.octris.core');
    }, \org\octris\core\registry::T_SHARED | \org\octris\core\registry::T_READONLY);

    // run application
    app\main::getInstance()->invoke(new app\create());
}

// $project = $opts['--project'];
// 
// $path = lima_cli::getAppDir();
// $path = $path . '/../../../../../libs/' . $project . '/app';
// 
// // load files
// $tmp = glob(realpath($path) . '/*');
// $files = array();
// 
// for ($i = 0, $len = count($tmp); $i < $len; ++$i) {
//     $key = basename($tmp[$i], '.class.php');
//     
//     $files[$project . '_app_' . $key] = $tmp[$i];
// }
// 
// // process files and generate diagramm
// print "processing ...\n";
// 
// $entry = $project . '_app_entry';
// 
// $fp = fopen($project . '.dot', 'w');
// fputs($fp, "digraph unix {\nsize=\"10,10\"\nnode [color=lightblue2, style=filled];\n");
// fputs($fp, "rankdir=LR;\n");
// 
// process($entry, $project, $files);
// 
// fputs($fp, "}\n");
// 
// $cmd = sprintf('/usr/local/graphviz/bin/dot -Tpdf -o%s.pdf %1$s.dot', $project);
// exec($cmd);
// 
// // **** //
// class lima_app {
// }
// 
// function process($page, $project, &$files) {
//     global $fp;
//     static $processed = array();
//     
//     print "$page\n";
//     
//     if (!array_key_exists($page, $files) || in_array($page, $processed)) {
//         return;
//     }
// 
//     $processed[] = $page;
//     $tmp_class = 'tmp_' . $page;
// 
//     // get next_pages from page
//     require_once($files[$page]);
// 
//     eval(sprintf('class %s extends %s { func' . 'tion __construct() {}}', $tmp_class, $page));
// 
//     $class = new ReflectionClass($tmp_class);
//     $tmp = $class->getProperty('next_pages');
//     
//     $obj = new $tmp_class();
//     $pages = $tmp->getValue($obj);
//     
//     asort($pages);
//     
//     // process next_pages
//     foreach ($pages as $k => $v) {
//         fputs($fp, sprintf(
//             "\"%s\" -> \"%s\" [label=%s];\n",
//             $page,
//             $v,
//             $k
//         ));
//         
//         process($v, $project, &$files);
//     }
// }
