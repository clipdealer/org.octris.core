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
            'h' => 'Header',
            'l' => 'License',
            'm' => 'Method',
            'v' => 'Variable'
        );
        /**/

        /**
         * Sorting criteria configuration.
         *
         * @octdoc  p:doc/$sort
         * @var     array
         */
        protected $sort = array(
            'types' => array('h' => 0, 'l' => 1, 'v' => 2, 'c' => 3, 'd' => 4, 'p' => 5, 'm' => 6)
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
                    'attributes' => array('args' => array())
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
                $row = trim(fgets($fp, 4096));
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
                        $tmp['source'] .= $row . "\n";
                    } elseif (preg_match('/' . $this->docblock[$tag]['source'][0] . '/', $row)) {
                        $source = true;
                        unset($attrib);

                        if ($this->docblock[$tag]['source'][1]) {
                            $tmp['source'] .= $row . "\n";
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
                                    continue;
                                }

                                $idx = count($tmp['attributes']['args']);

                                $tmp['attributes']['args'][$idx] = array(
                                    'name' => $_name,
                                    'type' => $_type,
                                    'text' => $_text
                                );

                                $attrib =& $tmp['attributes']['args'][$idx]['text'];
                                break;
                            case 'return':
                                try {
                                    list($_type, $_text) = preg_split('/ +/', $row, 2);
                                } catch(\Exception $e) {
                                    continue;
                                }

                                $tmp['attributes']['return'] = array(
                                    'name' => $_type,
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
        public function sort(array $doc)
        /**/
        {
            
            
            return $doc;
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

            $iterator    = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($src)
            );

            foreach ($iterator as $filename => $cur) {
                $path = preg_replace('|^' . $src . '|', '', $cur->getPathName());

                if (preg_match(':' . $include . ':', $path) && !preg_match(':' . $exclude . ':', $path) && preg_match(':' . $files . ':', basename($path))) {
                    // file to include in documentation
                    if (!($doc = $this->parse($cur->getPathName()))) {
                        continue;
                    }

                    $doc = $this->sort($doc);

                    print_r($doc);
                }
            }
        }
    }
}
