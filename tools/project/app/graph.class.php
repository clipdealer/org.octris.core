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
     * Create page graph of project.
     *
     * @octdoc      c:app/graph
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class graph extends \org\octris\core\app\cli\page
    /**/
    {
        /**
         * Name of project
         *
         * @octdoc  v:graph/$project
         * @var     string
         */
        protected $project;
        /**/

        /**
         * Output file.
         *
         * @octdoc  v:graph/$output
         * @var     string|null
         */
        protected $output = null;
        /**/

        /**
         * Output format.
         *
         * @octdoc  v:graph/$format
         * @var     string
         */
        protected $format = '';
        /**/

        /**
         * Dot command.
         *
         * @octdoc  v:graph/$cmd
         * @var     string
         */
        protected $cmd = '';
        /**/

        /**
         * Prepare page
         *
         * @octdoc  m:graph/prepare
         * @param   \org\octris\core\app\page       $last_page      Instance of last called page.
         * @param   string                          $action         Action that led to current page.
         * @return  mixed                                           Returns either page to redirect to or null.
         */
        public function prepare(\org\octris\core\app\page $last_page, $action)
        /**/
        {
            // import project name
            $args = \org\octris\core\provider::access('args');

            if ($args->isExist('p') && $args->isValid('p', \org\octris\core\validate::T_PROJECT)) {
                $project = $args->getValue('p');

                $path = \org\octris\core\app::getPath(\org\octris\core\app::T_PATH_LIBS, $project);

                if (!is_dir($path . '/app') || !is_file($path . '/app/entry.class.php')) {
                    die(sprintf("'%s' does not seem to be an application created with the OCTRiS framework", $project));
                }

                if ($args->isExist('o') && $args->isValid('o', \org\octris\core\validate::T_PRINTABLE)) {
                    $output = $args->getValue('o');
                    
                    $ext = strtolower(pathinfo($output, PATHINFO_EXTENSION));

                    if ($ext != 'pdf' && $ext != 'dot') {
                        die(sprintf("output file '%s' needs to have either '.pdf' or '.dot' extension\n", $output));
                    }

                    if ($ext == 'pdf' && ($cmd = `which dot`) == '') {
                        die("make sure graphviz is installed, graphviz 'dot' utility not found in path\n");
                    }

                    $dir = dirname($output);

                    if (!is_writable($dir)) {
                        die(sprintf("output directory '%s' is not writable\n", $dir));
                    }

                    $this->format = $ext;

                    if ($ext == 'pdf') {
                        $this->cmd = $cmd;
                        $this->output = $dir . '/' . basename($output, '.pdf');
                    } else {
                        $this->output = $output;
                    }
                }

                $cmd = sprintf('/usr/local/graphviz/bin/dot -Tpdf -o%s.pdf %1$s.dot', $project);


                $this->project = $project;
            } else {
                die("usage: ./graph.php -p project-name [-o output-file(.pdf|.dot)]\n");
            }
        }

        /**
         * Validate parameters.
         *
         * @octdoc  m:graph/validate
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
         * @octdoc  m:graph/dialog
         * @param   string                          $action         Action that led to current page.
         */
        public function dialog($action)
        /**/
        {
        }

        /**
         * Render.
         *
         * @octdoc  m:graph/render
         */
        public function render()
        /**/
        {
            $analyze = function($page) use (&$analyze) {
                static $processed = array();

                if (in_array($page, $processed)) {
                    return;
                }

                $processed[] = $page;

                try {
                    $class = new \ReflectionClass($page);
                } catch(\Exception $e) {
                    return;
                }

                if (!$class->hasProperty('next_pages')) {
                    return;
                }

                $tmp = $class->getProperty('next_pages');
                $tmp->setAccessible(true);

                $obj = new $page();
                $pages = $tmp->getValue($obj);

                asort($pages);

                // process next_pages
                foreach ($pages as $k => $v) {
                    printf(
                        "\"%s\" -> \"%s\" [label=%s];\n",
                        addcslashes('\\' . ltrim($page, '\\'), '\\'),
                        addcslashes('\\' . ltrim($v, '\\'), '\\'),
                        ($k == '' ? 'default' : $k)
                    );

                    $analyze("\\$v");
                }
            };

            if (!is_null($this->output)) {
                ob_start();
            }

            print "digraph unix {\nsize=\"10,10\"\nnode [color=lightblue2, style=filled];\n";
            print "rankdir=LR;\n";

            $entry = '\\' . str_replace('.', '\\', $this->project) . '\\app\\entry';

            $analyze($entry);

            print "}\n";

            if (!is_null($this->output)) {
                $tmp = ob_get_contents();

                ob_end_clean();

                if ($this->format == 'dot') {
                    file_put_contents($this->output, $tmp);
                } else {
                    $p = new \org\octris\core\app\cli\pipe(sprintf('dot  -Tpdf -o%s.pdf', escapeshellarg($this->output)));
                    $p->exec($tmp);
                }
            }
        }
    }
}
