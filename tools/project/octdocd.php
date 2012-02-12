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
        /* generic settings */
        body {
            margin:  0px auto;
            padding: 0;
            width:   950px;
            height:  100%;

            border-left: 2px outset #ccc;
            border-right: 1px solid #aaa;

            background-color: #fff;

            font-family: Verdana, Arial, Helvetica, sans-serif;
            font-size:   0.9em;
        }

        /* sidebar */
        #sidebar {
            position: fixed;
            width:    240px;
            top:      0;
            padding:  10px;
            height:   100%;

            border-right:     1px solid #fff;
            background-color: #ddd;
        }

        /* tools */
        #index {
            position: absolute;
            width:    240px;
            top:      160px;
            bottom:   0;
            overflow: auto;
        }
        #index ul {
            list-style:   none;
            margin-left:  0;
            padding-left: 1em;
            text-indent:  -1em;

            line-height: 150%;
        }
        #index ul li:before {
            content: '\00BB \0020';
        }

        /* main container */
        #main {
            min-height:  100%;
            margin-left: 260px;
            width:       690px;

            background-color: #eee;
        }

        /* content */
        #content {
            padding:    10px;
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

        /* styles for printing */
        @media print {
            #sidebar {
                display: none;
            }
            #content {
                display:          block;
                border:           0;
                background-color: #fff;
            }
        }
        </style>
        <script type="text/javascript">
        function $(id) {
            return new (function _node(node) {
                this.node = node;

                // set/get specified attribute of the node
                this.attr = function(name, value) {
                    var _value = node.getAttribute(name);
                    
                    if (typeof value != 'undefined') node.setAttribute(name, value);

                    return _value;
                }

                // iterate all nodes of specified tag below the node
                this.each = function(tag, cb) {
                    var tags = node.getElementsByTagName(tag);

                    for (var i = 0, cnt = tags.length; i < cnt; ++i) cb(new _node(tags[i]));
                }
            })(document.getElementById(id));
        }

        var request = (function() {
            var getRequest = (function() {
                if (window.ActiveXObject) {
                    return function() { return new ActiveXObject('Microsoft.XMLHTTP'); }
                } else if (window.XMLHttpRequest) { 
                    return function() { return new XMLHttpRequest(); }
                } else {
                    return function() { return false; }
                }
            })();

            function j2q(obj, pre) {
                var ret = [];
                var o   = (typeof obj != 'object' ? [obj] : obj);
                var k, v;

                pre = pre || '';

                if ('length' in o) {
                    for (var i = 0, len = o.length; i < len; ++i) {
                        v = o[i]; 
                        k = (pre != '' ? pre + '[' + i + ']' : i);

                        ret.push((typeof v == 'object' ? j2q(v, k) : k + '=' + encodeURIComponent(v)));
                    }
                } else {
                    for (k in o) {
                        v = o[k];
                        k = encodeURIComponent(k);
                        k = (pre != '' ? pre + '[' + k + ']' : k);

                        ret.push((v === null ? k + '=' : (typeof v == 'object' ? j2q(v, k) : k + '=' + encodeURIComponent(v))));
                    }
                }

                return ret.join('&');
            }

            return function(url, data, cb) {
                var request = getRequest();

                if (request) {
                    request.onreadystatechange = function() {
                        if (request.readyState == 4) {
                            var data  = {};
                            var error = false;

                            try {
                                data = (request.responseText != ''
                                        ? eval('(' + request.responseText + ')')
                                        : {});
                            } catch(e) {
                            }

                            // if ((error = ('error' in data && data['error'] != ''))) {
                            //     $('error').node.style.display = 'inline-block';
                            //     $('error').node.innerHTML = data['error'];
                            // } else {
                            //     $('error').node.style.display = 'none';
                            // }

                            cb(data, error);
                        }
                    }

                    request.open('POST', url, true);
                    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    request.send(j2q(data));
                } else {
                    alert('Not possible with your Browser!');
                }
            }
        })();

        window.onload = (function() {
            function load(name, target, cb) {
                cb = cb || function() {};

                request('/', {'ACTION': 'load', 'file': name}, function(data, error) {
                    if ('text' in data) {
                        $(target).node.innerHTML = data['text'];

                        window.scrollTo(0, 0);
                    }

                    cb(data, error);
                });
            }

            var recreate = false;

            return function() {
                request('/', {'ACTION': 'modules'}, function(data, error) {
                    if ('data' in data) {
                        var html = '';

                        for (var html = '', i = 0, cnt = data['data'].length; i < cnt; ++i) {
                            html += '<li><a href="javascript://" onclick="">' + data['data'][i] + '</a></li>';
                        }

                        $('index').node.innerHTML = '<h1>Modules</h1><ul>' + html + '</ul>';

                        window.scrollTo(0, 0);
                    }
                });

                $('bt_recreate').node.onclick = function() {
                    if (!recreate) {
                        $('bt_recreate').node.className = 'working';
    
                        request('/', {'ACTION': 'recreate'}, function(data, error) {
                            var to = 1500;
                            var cb = function() {
                                request('/', {'ACTION': 'poll'}, function(data, error) {
                                    if (!('status' in data) || data['status'] != 'ok') {
                                        window.setTimeout(cb, to);
                                        return;
                                    }

                                    if (error) {
                                        $('bt_recreate').node.className = 'recreate';
                                        recreate = false;
                                    } else {
                                        load('index.html', 'index', function() {
                                            $('bt_recreate').node.className = 'recreate';
                                            recreate = false;

                                            $('index').each('a', function(tag) {
                                                var href = tag.attr('href', 'javascript://');
                                                tag.node.onclick = function() {
                                                    load(href, 'content');
                                                }
                                            });
                                        });
                                    }
                                });
                            }

                            if (!error) window.setTimeout(cb, to);
                        });

                        recreate = true;
                    }
                }

                $('bt_print').node.onclick = function() {
                    window.print();
                }
            }
        })();
        </script>
    </head>
    <body>
        <div id="sidebar">
            <div id="menu">
                <strong>Documentation Browser</strong>
                <ul>
                    <li><a href="javascript://">Modules</a></li>
                    <li><a id="bt_recreate" href="javascript://">Recreate</a></li>
                    <li><a id="bt_print" href="javascript://">Print</a></li>
                </ul>

                <string>Search</string><br />
                <input type="search" style="display: block; width: 100%;" />
            </div>

            <div id="index">
            </div>
        </div>

        <div id="main">
            <div id="content">
            </div>
        </div>
    </body>
</html>
