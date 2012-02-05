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
        protected $format = '';
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
            'c' => 2, 'i' => 2, 't' => 2,
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
                die("usage: ./doc.php -p project-name\n");
            }

            exec('which pandoc', $out, $ret);

            if ($ret !== 0) {
                die("unable to locate 'pandoc' in path\n");
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
         * Sort parsed documentation in various ways.
         *
         * @octdoc  m:doc/sort
         * @param   array                           $doc            Unsorted documentation.
         * @return  array                                           Sorted documentation.
         */
        protected function sort(array $doc)
        /**/
        {
            $sort =& $this->sort;

            usort($doc, function($a, $b) use ($sort) {
                // sort by docblock type
                if (!isset($sort['types'][$a['type']]) || !isset($sort['types'][$b['type']])) {
                    if (!isset($sort['types'][$a['type']])) {
                        $this->log(sprintf("unknown type '%s' in:", $a['type']), $a);
                    }
                    if (!isset($sort['types'][$b['type']])) {
                        $this->log(sprintf("unknown type '%s' in:", $b['type']), $b);
                    }

                    return -1;
                }

                if (($diff = $sort['types'][$a['type']] - $sort['types'][$b['type']]) !== 0) {
                    return $diff;
                }

                // sort by scope
                $diff = $a['scope'] - $b['scope'];

                return $diff;
            });

            return $doc;
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
                array('file', '/dev/null', 'w')
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

                // print_r($part);
            }

            fclose($fp);

            return true;
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
                die("unable to create temporary directory '$tmp_name'\n");
            }

            print "$tmp_name\n";

            mkdir($tmp_name . '/tmp');
            mkdir($tmp_name . '/doc');

            foreach ($iterator as $filename => $cur) {
                $path = preg_replace('|^' . $src . '|', '', $cur->getPathName());

                if (preg_match(':' . $include . ':', $path) && !preg_match(':' . $exclude . ':', $path) && preg_match(':' . $files . ':', basename($path))) {
                    // file to include in documentation
                    if (!($doc = $this->parse($cur->getPathName()))) {
                        continue;
                    }

                    $doc   = $this->sort($doc);
                    $scope = dirname($path) . '/' . preg_replace('/' . $strip . '/', '', basename($path));
                    $name  = preg_replace('/[\/\.]/', '_', ltrim($scope, '/'));

                    $parts[str_replace('/', '|', $scope)] = array(
                        'scope' => $scope,
                        'file'  => ($name = $tmp_name . '/doc/' . $name . '.html')
                    );

                    $this->write($name, $doc); //$scope, $doc);
                }
            }

            ksort($parts);

            print_r($parts);

            // `rm -rf $tmp_name 1>/dev/null 2>&1`;
        }
    }
}
