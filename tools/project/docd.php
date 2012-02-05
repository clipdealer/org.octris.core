#!/usr/bin/env php
<?php

/**
 * Documentaton server.
 *
 * @octdoc      h:project/docd
 * @copyright   copyright (c) 2012 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
/**/

$sapi = php_sapi_name();

if ($sapi == 'cli') {
    // restart docd using php's webserver
    $cmd    = exec('which php', $out, $ret);
    $router = __FILE__;

    if ($ret !== 0) {
        die("unable to locate 'php' in path\n");
    }

    $host = '127.0.0.1';
    $port = '8888';

    exec(sprintf('((%s -S %s:%s %s 1>/dev/null 2>&1 &) &)', $cmd, $host, $port, $router));

    die(sprintf("docd server started on '%s:%s'\n", $host, $port));
} elseif ($sapi != 'cli-server') {
    die("unable to execute docd server\n");
}

if (isset($_GET['recreate'])) {
    print $_GET['recreate'];
}

?>
<html>
    <head>
        <title>org.octris.core -- documentation server</title>
        <style type="text/css">
        body {
            margin: 0 auto;
            width:  780px;

            background-color: #fff;

            font-family: Verdana, Arial, Helvetica, sans-serif;
            font-size:   0.9em;

            /* to hide ugly unavoidable shebang */
            line-height: 0;
            color:       transparent;
        }
        #content {
            border-left:  1px solid #ddd;
            border-right: 1px solid #ddd;

            padding:     5px;

            line-height: 100%;
            min-height:  100%;

            color:            #000;
            background-color: #eee;
        }
        #content pre {
            border:           1px solid #ccc;
            background-color: #fff;
            padding:          5px;
        }
        #content dt {
            margin-top:  20px;
            font-weight: bold;
            font-size: 1em;
        }
        #content dd {
            margin-top: 10px;
        }
        #content dd table {
            font-size: 1em;
            color:     #000;
        }
        #content dd table thead tr th {
            font-size:     1em;
            text-align:    left;
            border-bottom: 1px solid #ccc;
        }
        #content dd table tbody tr td {
            border-bottom:  1px solid #ccc;
            vertical-align: top;
        }
        </style>
    </head>
    <body>
        <div id="content">

        <?php

        require_once('/var/folders/dm/yfkmpjkx3c799ss7s_sdw_p00000gn/T/octdoc.7Zy1rS4VO5/doc/libs_tpl_compiler.html');

        ?>

        </div>
    </body>
</html>