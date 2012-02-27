#!/usr/bin/env php
<?php

/**
 * Documentaton server.
 *
 * @octdoc      h:project/octdocd
 * @copyright   copyright (c) 2012 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
/**/

$sapi = php_sapi_name();
$info = posix_getpwuid(posix_getuid());
$home = $info['dir'] . '/.octdoc';

if ($sapi == 'cli') {
    // test php version
    $version = '5.4.0RC7';

    if (version_compare(PHP_VERSION, $version) < 0) {
        die(sprintf("unable to start webserver. please upgrade to PHP version >= '%s'. your version is '%s'\n", $version, PHP_VERSION));
    }

    // create working directory
    if (!is_dir($home)) {
        mkdir($home, 0755);
    }

    // leave, if octdocd server is already running
    exec('ps ax | grep octdocd.php | grep -v grep | grep "\-S"', $out, $ret);

    if ($ret === 0) {
        die("octdocd is already running\n");
    }

    // restart octdocd using php's webserver
    $cmd    = exec('which php', $out, $ret);
    $router = __FILE__;

    if ($ret !== 0) {
        die("unable to locate 'php' in path\n");
    }

    $host = '127.0.0.1';
    $port = '8888';

    exec(sprintf('((%s -d output_buffering=on -S %s:%s %s 1>/dev/null 2>&1 &) &)', $cmd, $host, $port, $router), $out, $ret);
    exec('ps ax | grep octdocd.php | grep -v grep | grep "\-S"', $out, $ret);

    if ($ret !== 0) {
        die(sprintf("unable to start webserver on '%s:%s'\n", $host, $port));
    } else {
        die(sprintf("octdocd server started on '%s:%s'\n", $host, $port));
    }
} elseif ($sapi != 'cli-server') {
    die("unable to execute octdocd server in environment '$sapi'\n");
}

// remove shebang from output
ob_end_clean();

// view controller
if (isset($_POST['ACTION'])) {
    $return = array('status' => '', 'error' => '');
    $action = $_POST['ACTION'];

    switch($action) {
        case 'modules':
            $return['data'] = array_map(function($v) {
                return basename($v);
            }, glob(getenv('OCTRIS_BASE') . '/work/*', GLOB_ONLYDIR));
            break;
        case 'load':
            if (!isset($_POST['file']) || !is_file(($file = $home . '/doc/' . $_POST['file']))) {
                $return['error'] = "Unable to load '$file'";
                break;
            }

            $return['text'] = file_get_contents($file);

            break;
        case 'recreate':
        case 'poll':
            // test if documentation creator is still running
            exec('ps ax | grep doc.php | grep -v grep', $out, $ret);

            if ($ret === 1) {
                $return['status'] = 'ok';
            }

            if ($action == 'poll') {
                break;
            }

            exec(sprintf('((%s -p org.octris.core 2>/dev/null | (cd %s && tar -xpf -) &) &)', __DIR__ . '/doc.php', $home));
            break;
    }

    die(json_encode($return));
}

// render documentation browser
?>
<html>
    <head>
        <title>org.octris.core -- documentation server</title>
        <style type="text/css">
        /* generic styles */
        body {
            font-family: Verdana, Arial, Helvetica, sans-serif;

            background-color: #fff;
        }
        form {
            display: inline;
        }
        pre {
            border:           1px solid #ddd;
            background-color: #eee;
            padding:          5px;
        }
        dt {
            font-weight: bold;
            font-style:  italic;
            margin:      5px 0;
        }
        table {
            border: 1px solid #ddd;
        }
        table thead tr th {
            text-align: left;
            background-color: #ccc;
        }
        table tbody tr:nth-child(odd) { 
            background-color: #eee; 
        }
        table tbody tr:nth-child(even) { 
            background-color: #fff; 
        }
        ul {
            list-style:   none;
            padding-left: 1em;
            text-indent:  -1em;
            margin:       5px 0;
        }
        ul li:before {
            content: "\00BB \0020";
        }

        /* navigation */
        body.nav {
            background:  #000 url('data:image/gif;base64,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') right center no-repeat;
            color:       #fff;
            margin:      0 5px;
            padding:     0;
            line-height: 50px;
        }
        </style>
    </head>

<?php
    if (!isset($_GET['page'])) {
?>
    <frameset rows="50px,*" frameborder="1" framespacing="1" border="1">
        <frame src="/?page=nav" name="nav" scrolling="no" noresize="noresize" frameborder="1" />
        <frame src="/?page=doc" name="doc" />
    </frameset>
<?php
    } elseif ($_GET['page'] == 'doc') {
        if (!isset($_GET['module']) || $_GET['module'] == '') {
?>
    <body>
        No module selected!
    </body>
<?php
        } elseif (isset($_GET['recreate'])) {
            mkdir($home . '/doc/' . $_GET['module'], 0777, true);

            passthru(sprintf(
                '((%s -p org.octris.core 2>/dev/null | (cd %s && tar -xpf -) &) &)', 
                __DIR__ . '/doc.php', 
                $home
            ));
?>
    <body>
        <script type="text/javascript">
        document.location.href='/?page=doc&module=<?php print $_GET['module']; ?>';
        </script>
    </body>
<?php
        } elseif (!is_dir($home . '/doc/' . $_GET['module'])) {
            exec('ps ax | grep doc.php | grep ' . $_GET['module'] . ' | grep -v grep', $out, $ret);

            if ($ret === 0) {
?>
    <body>
        Documentationf or module being created.
        <script type="text/javascript">
        window.setTimeout(function() {
            document.location.reload(true);
        }, 1000);
        </script>
    </body>
<?php
            } else {
?>
    <body>
        No documentation available for selected module. <a href="/?page=doc&amp;module=<?php print $_GET['module']; ?>&amp;recreate=1">create</a>?
    </body>
<?php
            }
        } else {
?>
    <frameset cols="30%,70%" frameborder="1" framespacing="5" border="5">
        <frame src="/?page=index&amp;module=<?php print $_GET['module']; ?>" name="index" />
        <frame src="/?page=content" name="content" />
    </frameset>
<?php
        }
    } elseif ($_GET['page'] == 'nav') {
?>

    <body class="nav">
        <form action="/" method="GET" target="doc">
            <input type="hidden" name="page" value="doc" />
            Modules:
            <select name="module" onchange="this.form.submit();">
                <option name=""></option>
<?php
    if (($base = getenv('OCTRIS_BASE'))) {
        $files = glob($base . '/work/*');

        foreach ($files as $file) {
            $name = basename($file);

            if (!preg_match('/^[a-z]{2,4}\.([a-z0-9]+([a-z0-9\-]*[a-z0-9]+|)\.){2}$/', $name . '.')) {
                continue;
            }

            print '<option value="' . $name . '">' . $name . '</option>';
        }
    }
?>
            </select>
        </form>&nbsp;&nbsp;&nbsp;
<!--        <form>
            <strong>Search:</strong>
            <input type="search" />
        </form> -->
    </body>
<?php
    } elseif ($_GET['page'] == 'index') {
?>
    <body>
<?php
    $content = file_get_contents($home . '/doc/' . $_GET['module'] . '/index.html');

    $content = preg_replace(
        '/<a href="([^"]+)">/', 
        sprintf(
            '<a target="content" href="/?page=content&amp;file=%s/\1">',
            $_GET['module']
        ),
        $content
    );

    print $content;
?>
    </body>
<?php
    } elseif ($_GET['page'] == 'content') {
?>
    <body>
<?php
        if (isset($_GET['file'])) {
            print file_get_contents($home . '/doc/' . $_GET['file']);
        }
?>
    </body>
<?php
    }
?>
</html>