<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\project\app {
    use \org\octris\core\app as app;
    use \org\octris\core\config as config;
    use \org\octris\core\validate as validate;

    /**
     * Create documentation for a project and stream it to STDOUT.
     *
     * @octdoc      c:app/doc
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class doc extends \org\octris\core\app\cli\page
    /**/
    {
        /**
         * Name of project
         *
         * @octdoc  p:doc/$project
         * @var     string
         */
        protected $project;
        /**/

        /**
         * Output file.
         *
         * @octdoc  p:doc/$output
         * @var     string|null
         */
        protected $output = null;
        /**/

        /**
         * Output format.
         *
         * @octdoc  p:doc/$format
         * @var     string
         */
        protected $format = 'octdoc';
        /**/

        /**
         * Supported output formats.
         *
         * @octdoc  p:doc/$formats
         * @var     array
         */
        protected $formats = array(
            'html', 'octdoc'
        );
        /**/

        /**
         * Dot command.
         *
         * @octdoc  p:doc/$cmd
         * @var     string
         */
        protected $cmd = '';
        /**/

        /**
         * Docblock definitions.
         *
         * @octdoc  p:doc/$docblock
         * @var     array
         */
        protected $docblock = array(
            '/**'   => array(
                        'start'  => '^\/\*\*',
                        'doc'    => '^\*',
                        'source' => array('^\*\/', false),
                        'end'    => '^\/\*\*\/'
                    ),
            '#**'   => array(
                        'start'  => '^#\*\*',
                        'doc'    => '^#',
                        'source' => array('^[^#]*', true),
                        'end'    => '^#\*\*'
                    )
        );
        /**/

        /**
         * Docblock types.
         *
         * @octdoc  p:doc/$types
         * @var     array
         */
        protected $types = array(
            'c' => 'Class',
            'd' => 'Constant',
            'f' => 'Function',
            'h' => 'Header',
            'i' => 'Interface',
            'l' => 'License',
            'm' => 'Method',
            'p' => 'Property',
            't' => 'Trait',
            'v' => 'Variable'
        );
        /**/

        /**
         * Docblock attributes.
         *
         * @octdoc  p:doc/$attributes
         * @var     array
         */
        protected $attributes = array(
            'author'    => 'Author',
            'copyright' => 'Copyright',
            'license'   => 'License',
            'package'   => 'Package',

        );
        /**/

        /**
         * Documentation sections.
         *
         * @octdoc  p:doc/$sections
         * @var     array
         */
        protected $sections = array(
            'libs'   => 'Libraries',
            'libsjs' => 'Javascript Libraries',
            'styles' => 'Stylesheets',
            'tools'  => 'Tools',
            ''       => 'Misc'
        );
        /**/

        /**
         * Sorting criteria configuration.
         *
         * @octdoc  p:doc/$sort
         * @var     array
         */
        protected $sort = array(
            'types' => array(
                'h' => 0, 
                'l' => 1, 
                'v' => 2, 
                'f' => 3, 
                'c' => 4, 'i' => 4, 't' => 4,
                'd' => 5, 
                'p' => 6, 
                'm' => 7
            ),
            'attributes' => array(
                'package', 
                'license',
                'copyright',
                'author',
                'extends',
                'deprecated',
                'since',
                'see',
                'tutorial',
                'example',
                'abstract',
                'static',
                'param',
                'return',
                'todo'
            ),
            'sections' => array(
                'libs', 'libsjs', 'styles', 'tools', ''
            ),
            'indextypes' => array(
                'h', 'c', 't', 'i'
            )
        );
        /**/

        /**
         * Number of maximum lines to include in source block.
         *
         * @octdoc  p:doc/$source_lines
         * @var     int
         */
        protected $source_lines = 9;
        /**/

        /**
         * Documentation depth.
         *
         * @octdoc  p:doc/$depth
         * @var     array
         */
        protected $depth = array(
            'h' => 1,
            'v' => 1, 'f' => 1,
            'c' => 1, 'i' => 1, 't' => 1,
            'd' => 3, 'p' => 3, 'm' => 3,

            'scope' => 5
        );
        /**/

        /**
         * Configuration of files to parse documentation in.
         *
         * @octdoc  p:doc/$extensions
         * @var     array
         */
        protected $files = array('.+\.php$', '.+\.js$', '.+\.css$', '^Makefile(|\..+)$');
        /**/
        
        /**
         * Directories to scan for creating documentation.
         *
         * @octdoc  p:doc/$directories
         * @var     array
         */
        protected $directories = array(
            '^/'
        );
        /**/

        /**
         * Directories to skip when creating documentation.
         *
         * @octdoc  p:doc/$skip_directories
         * @var     array
         */
        protected $skip_directories = array(
            '/CVS/', '/\.svn/', '^/\.git/',
            '^/etc/',
            '^/data/',
            '^/tests/',
            '^/tools/.*/app/', '^/tools/.*/data/', '^/tools/.*/libs/'
        );
        /**/

        /**
         * Extensions to strip from filenames.
         *
         * @octdoc  p:doc/$strip_extensions
         * @var     array
         */
        protected $strip_extensions = array(
            '\.class\.php$', '\.php$', '\.js$', '\.css$'
        );
        /**/

        /**
         * Prepare page
         *
         * @octdoc  m:doc/prepare
         * @param   \org\octris\core\app\page       $last_page      Instance of last called page.
         * @param   string                          $action         Action that led to current page.
         * @return  mixed                                           Returns either page to redirect to or null.
         */
        public function prepare(\org\octris\core\app\page $last_page, $action)
        /**/
        {
            // import project name
            $args = \org\octris\core\provider::access('args');

            if ($args->isExist('p') && ($project = $args->getValue('p', \org\octris\core\validate::T_PROJECT))) {
                $this->project = $project;
            } else {
                $this->log('usage: ./doc.php -p project-name [-f output-format]');
                die(1);
            }

            exec('which pandoc', $out, $ret);

            if ($ret !== 0) {
                $this->log('unable to locate \'pandoc\' in path');
                die(1);
            }
        }

        /**
         * Validate parameters.
         *
         * @octdoc  m:doc/validate
         * @param   string                          $action         Action that led to current page.
         * @return
         */
        public function validate()
        /**/
        {
            return true;
        }

        /**
         * Implements dialog.
         *
         * @octdoc  m:doc/dialog
         * @param   string                          $action         Action that led to current page.
         */
        public function dialog($action)
        /**/
        {
        }

        /**
         * Output message to STDERR.
         *
         * @octdoc  m:doc/log
         * @param   string                          $msg            Message to output.
         * @param   array                           $payload        Optional additional information to output.
         */
        protected function log($msg, array $payload = null)
        /**/
        {
            fputs(STDERR, trim($msg) . "\n");

            if (!is_null($payload)) {
                fputs(STDERR, sprintf("  file: %s\n", $payload['file']));
                fputs(STDERR, sprintf("  line: %s\n", $payload['line']));
            }

            fputs(STDERR, "\n");
        }

        /**
         * Parse a file and extract it's documentation.
         *
         * @octdoc  m:doc/parse
         * @param   string                          $file           File to parse.
         * @return  bool|array                                      Returns false in case of an error or an array with the parsed documentation.
         */
        protected function parse($file)
        /**/
        {
            if (!is_readable($file) || !($fp = fopen($file, 'r'))) {
                return false;
            }

            $open = function($row) {
                foreach ($this->docblock as $tag => $def) {
                    if (preg_match('/' . $def['start'] . '/', $row)) {
                        return $tag;
                    }
                }

                return false;
            };
            $init = function() use ($file) {
                return array(
                    'file'       => $file,
                    'line'       => 0,
                    'scope'      => '',
                    'source'     => '',
                    'text'       => '',
                    'type'       => '',
                    'attributes' => array('param' => array())
                );
            };

            $opened = false;
            $source = false;
            $attrib = null;
            $tag    = false;
            $tmp    = $init();

            $return = array();
            $line   = 0;

            while (true) {
                $row = ltrim($raw = rtrim(fgets($fp, 4096)));
                $eof = feof($fp);

                ++$line;

                if (!$opened && !$eof) {
                    if (($tag = $open($row)) !== false) {
                        $opened = true;

                        $tmp['line'] = $line;
                    }
                } else {
                    if ($eof || preg_match('/' . $this->docblock[$tag]['end'] . '/', $row)) {
                        $opened = $source = $tag = false;
                        unset($attrib);

                        // TODO: output
                        if ($tmp['scope'] !== '') {
                            $return[] = $tmp;
                        }

                        $tmp = $init();

                        if ($eof) break;
                    } elseif ($source) {
                        $tmp['source'] .= $raw . "\n";
                    } elseif (preg_match('/' . $this->docblock[$tag]['source'][0] . '/', $row)) {
                        $source = true;
                        unset($attrib);

                        if ($this->docblock[$tag]['source'][1]) {
                            $tmp['source'] .= $raw . "\n";
                        }
                    } elseif (preg_match('/' . $this->docblock[$tag]['doc'] . '/', $row, $match)) {
                        $row = substr($row, strlen($match[0]));

                        if (preg_match('/ *@([a-z]+)/', $row, $match)) {
                            $row = trim(substr($row, strlen($match[0])));
                            unset($attrib);

                            switch ($match[1]) {
                            case 'octdoc':
                                if (preg_match('/^([a-z]):(.*)$/', $row, $match)) {
                                    $tmp['scope'] = $match[2];
                                    $tmp['type']  = $match[1];
                                }
                                break;
                            case 'param':
                                try {
                                    list($_type, $_name, $_text) = preg_split('/ +/', $row, 3);
                                } catch(\Exception $e) {
                                    $this->log('unable to parse @param in:', $tmp);
                                    continue;
                                }

                                $idx = count($tmp['attributes']['param']);

                                $tmp['attributes']['param'][$idx] = array(
                                    'name' => $_name,
                                    'type' => $_type,
                                    'text' => $_text
                                );

                                $attrib =& $tmp['attributes']['param'][$idx]['text'];
                                break;
                            case 'return':
                                try {
                                    list($_type, $_text) = preg_split('/ +/', $row, 2);
                                } catch(\Exception $e) {
                                    $this->log('unable to parse @return in:', $tmp);
                                    continue;
                                }

                                $tmp['attributes']['return'] = array(
                                    'type' => $_type,
                                    'text' => $_text
                                );
                                break;
                            default:
                                $tmp['attributes'][$match[1]] = $row . "\n";
                                $attrib =& $tmp['attributes'][$match[1]];
                                break;
                            }
                        } elseif (isset($attrib) !== false) {
                            $attrib .= $row . "\n";
                        } else {
                            $tmp['text'] .= $row . "\n";
                        }
                    }
                }
            }
            
            fclose($fp);

            return $return;
        }

        /**
         * Pipe something through pandoc. Either a file or a string can be converted.
         *
         * @octdoc  m:doc/pandoc
         * @param   string                          $from           Convert from format.
         * @param   string                          $to             Convert to format.
         * @param   string|null                     $file           File to convert.
         * @param   string|null                     $string         String to convert.
         */
        protected function pandoc($from, $to, $file, $string = null)
        /**/
        {
            $return = '';

            if (is_null($file) && is_null($string)) {
                return $return;
            }

            $cmd = sprintf(
                'pandoc -f %s -t %s',
                escapeshellarg($from),
                escapeshellarg($to)
            );

            $descriptors = array(
                array('pipe', 'r'),
                array('pipe', 'w'),
                STDERR
                // array('file', '/dev/null', 'w')
            );

            $pipes = array();

            if (!($pp = proc_open($cmd, $descriptors, $pipes))) {
                $this->log('unable to start pandoc');
                return $return;
            }

            do {
                if (!is_null($file)) {
                    if (!($fp = fopen($file, 'r'))) {
                        break;
                    }

                    while (!feof($fp)) {
                        fputs($pipes[0], fgets($fp));
                    }

                    fclose($fp);
                } elseif (!is_null($string)) {
                    fputs($pipes[0], $string);
                }

                fclose($pipes[0]);

                while (!feof($pipes[1])) {
                    $return .= fgets($pipes[1]);
                }

                fclose($pipes[1]);

                proc_close($pp);
            } while(false);

            return $return;
        }

        /**
         * Write documentation index to temporary directory.
         *
         * @octdoc  m:doc/index
         * @param   string                          $file           File to write index into.
         * @param   array                           $doc            Generic module documentation.
         * @param   array                           $source         Documentation parts extracted from source code.
         */
        protected function index($file, array $doc, array $source)
        /**/
        {
            if (!($fp = fopen($file, 'w'))) {
                $this->log("unable to open file '$file' for writing");
                return false;
            }

            fputs($fp, "<h1>Index</h1>\n");

            // generic documentation index
            if (count($doc) > 0) {
                fputs($fp, "<h2>Documentation</h2>\n");

                // TODO
            }

            // API documentation index
            $indent = 0;
            $output = function($file = null) use ($fp, &$indent) {
                if (!is_null($file)) {
                    $scope = explode('/', ltrim(dirname($file['scope']), '/'));
                    array_shift($scope);

                    $cnt = count($scope);
                } else {
                    $cnt = 0;
                }

                if ($cnt < $indent) {
                    for (; $indent > $cnt; --$indent) {
                        fputs($fp, "</li></ul>\n");
                    }
                } elseif ($cnt > $indent + 1) {
                    for (; $indent < $cnt; ++$indent) {
                        fputs($fp, sprintf("<li>%s<ul>", $scope[$indent]));
                    }
                } elseif ($cnt > $indent) {
                    fputs($fp, "<ul>\n");
                    ++$indent;
                }

                if (!is_null($file)) {
                    fputs($fp, sprintf("<li><a href=\"%s\">%s</a>\n", basename($file['file']), htmlentities($file['name'])));
                }
            };

            foreach ($source as $section => $part) {
                // section header
                fputs($fp, sprintf("<h2>%s</h2>\n", $this->sections[$section]));

                foreach ($part as $type => $scope) {
                    // type header
                    fputs($fp, sprintf("<h3>%s</h3>\n", $this->types[$type]));
                    fputs($fp, "<ul>\n");

                    foreach ($scope as $file) {
                        $output($file);
                    }

                    $output();
    
                    fputs($fp, "</ul>\n");
                }
            }

            fclose($fp);
        }

        /**
         * Write documentation to temporary directory.
         *
         * @octdoc  m:doc/write
         * @param   string                          $file           File to write documentation into.
         * @param   array                           $doc            Documentation to write.
         */
        protected function write($file, array $doc)
        /**/
        {
            if (!($fp = fopen($file, 'w'))) {
                $this->log("unable to open file '$file' for writing");
                return false;
            }

            $type = '';

            foreach ($doc as $part) {
                // write a section header
                if ($type != $part['type']) {
                    $type = $part['type'];

                    fputs($fp, sprintf("<h%1\$d>%2\$s</h%1\$d>\n", $this->depth[$type], htmlentities($this->types[$type])));
                }

                if (($pos = strpos($part['scope'], '/')) !== false) {
                    fputs($fp, sprintf(
                        "<h%1\$d>%2\$s</h%1\$d>\n", 
                        $this->depth['scope'], 
                        substr($part['scope'], $pos + 1)
                    ));
                }

                // write description
                if (trim($part['text']) != '') {
                    fputs($fp, $this->pandoc('markdown', 'html', null, $part['text']));
                }

                // write included source code
                if (trim($tmp = $part['source']) != '') {
                    // cut preceeding spaces but keep indentation
                    if (preg_match('/^( +)/', $tmp, $match)) {
                        $tmp = preg_replace('/^' . $match[1] . '/m', '', $tmp);
                    }

                    // renove trailing spaces and cut off last newline
                    $tmp = preg_replace('/[ ]+$/m', '', rtrim($tmp));

                    // remove source lines, if there are too much
                    if (substr_count($tmp, "\n") > $this->source_lines) {
                        $tmp = preg_split("/\n/", $tmp, $this->source_lines + 1);
                        $tmp = array_reverse($tmp);
                        array_shift($tmp);

                        while (count($tmp) > 0 && trim($tmp[0]) === '') {
                            array_shift($tmp);
                        }

                        if (count($tmp) > 0) {
                            preg_match('/^[ ]*/', $tmp[0], $match);
                            
                            $tmp = array_reverse($tmp);

                            $tmp[] = $match[0] . '...';
                            $tmp = implode("\n", $tmp);
                        } else {
                            $tmp = '';
                        }

                    }

                    if ($tmp != '') fputs($fp, sprintf("<pre>%s</pre>\n", htmlentities($tmp)));
                }

                // write additional attributes
                fputs($fp, "<dl>\n");

                foreach ($this->sort['attributes'] as $name) {
                    if (!isset($part['attributes'][$name])) continue;

                    $attr =& $part['attributes'][$name];

                    if (is_array($attr) && count($attr) == 0) continue;

                    $dd = '';

                    switch ($name) {
                    case 'deprecated':
                        $dd .= "Yes";
                        break;
                    case 'param':
                        $dd .= "<table width=\"100%\"><thead><tr>\n";
                        $dd .= "<th>Name</th><th>Type</th><th>Description</th>\n";
                        $dd .= "</tr></thead><tbody>\n";

                        foreach ($attr as $r) {
                            $dd .= sprintf(
                                "<tr><td>%s</td><td>%s</td><td>%s</td></tr>\n",
                                $r['name'], $r['type'], $r['text']
                            );
                        }

                        $dd .= "</tbody></table>\n";
                        break;
                    case 'return':
                        $dd .= "<table width=\"100%\"><thead><tr>\n";
                        $dd .= "<th>Type</th><th>Description</th>\n";
                        $dd .= "</tr></thead><tbody>\n";

                        $dd .= sprintf(
                            "<tr><td>%s</td><td>%s</td></tr>\n",
                            $attr['type'], $attr['text']
                        );

                        $dd .= "</tbody></table>\n";
                        break;
                    default:
                        $dd = $attr;
                        break;
                    }

                    if ($dd) {
                        fputs($fp, sprintf("<dt>%s</dt>\n", htmlentities($name)));
                        fputs($fp, sprintf("<dd>%s</dd>\n", $dd));
                    }
                }

                fputs($fp, "</dl>\n");
            }

            fclose($fp);

            return true;
        }

        /**
         * Create organizational structure for documentation.
         *
         * @octdoc  m:doc/organize
         * @param   array                           $parts          Documentation parts to organize.
         * @return  array                                           Organized parts.
         */
        protected function organize(array $parts)
        /**/
        {
            // create tree structure of documentation
            $return = array();

            foreach ($parts as $part) {
                // sections
                $section = explode('/', ltrim(dirname($part['scope']), '/'))[0];

                if ($section == '') { print_r($part); die; }

                if (!isset($return[$section])) $return[$section] = array();

                // type
                $type = $part['type'];

                if (!isset($return[$section][$type])) $return[$section][$type] = array();

                // part
                $return[$section][$type][] = $part;
            }

            // sort tree structure
            uksort($return, function($a, $b) {
                return (array_search($a, $this->sort['sections']) - array_search($b, $this->sort['sections']));
            });

            foreach ($return as &$types) {
                uksort($types, function($a, $b) {
                    return (array_search($a, $this->sort['indextypes']) - array_search($b, $this->sort['indextypes']));
                });

                foreach ($types as &$type) {
                    usort($type, function($a, $b) {
                        return strcmp($a['scope'], $b['scope']);
                    });
                }
            }

            return $return;
        }

        /**
         * Render.
         *
         * @octdoc  m:doc/render
         */
        public function render()
        /**/
        {
            $src = \org\octris\core\app::getPath(\org\octris\core\app::T_PATH_WORK, $this->project);

            $include = '(' . implode('|', $this->directories) . ')';
            $exclude = '(' . implode('|', $this->skip_directories) . ')';
            $files   = '(' . implode('|', $this->files) . ')';
            $strip   = '(' . implode('|', $this->strip_extensions) . ')';

            $parts = array();

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($src)
            );

            $tmp_dir = sys_get_temp_dir();
            if (!($tmp_name = trim(`mktemp -d $tmp_dir/octdoc.XXXXXXXXXX 2>/dev/null`)) || !is_dir($tmp_name) || !is_writable($tmp_name)) {
                $this->log('unable to create temporary directory \'$tmp_name\'');
                die(1);
            }

            mkdir($tmp_name . '/tmp');
            mkdir($tmp_name . '/doc');

            foreach ($iterator as $filename => $cur) {
                $path = preg_replace('|^' . $src . '|', '', $cur->getPathName());

                if (preg_match(':' . $include . ':', $path) && !preg_match(':' . $exclude . ':', $path) && preg_match(':' . $files . ':', basename($path))) {
                    // file to include in documentation
                    if (!($doc = $this->parse($cur->getPathName()))) {
                        continue;
                    }

                    $scope = dirname($path) . '/' . preg_replace('/' . $strip . '/', '', basename($path));
                    $name  = preg_replace('/[\/\.]/', '_', ltrim($scope, '/'));

                    if (!in_array($doc['0']['type'], array('h', 'c', 'i', 't'))) {
                        $this->log("first part in a file must be of type 'class', 'header', 'interface' or 'trait'", $doc[0]);

                        continue;
                    }

                    $parts[] = array(
                        'scope' => $scope,
                        'file'  => ($name = $tmp_name . '/doc/' . $name . '.html'),
                        'type'  => $doc[0]['type'],
                        'name'  => $doc[0]['scope']
                    );

                    //$this->write($name, $doc); //$scope, $doc);
                }
            }

            $parts = $this->organize($parts);

            $this->index('/tmp/index.html', array(), $parts);
            // $this->index($tmp_name . '/doc/index.html', array(), $parts);

            passthru("cd $tmp_name && tar -cf - doc/", $ret);

            // if ($ret !== 0) {
            //     $this->log("error creating tar from documentation '$ret'");
            // }

            // `rm -rf $tmp_name 1>/dev/null 2>&1`;
        }
    }
}
