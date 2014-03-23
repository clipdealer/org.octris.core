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
    use \org\octris\core\app\cli\stdio as stdio;
    use \org\octris\core\validate as validate;

    /**
     * Lint a project.
     *
     * @octdoc      c:app/lint
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class lint extends \org\octris\core\app\cli\page
    /**/
    {
        /**
         * Application data.
         *
         * @octdoc  p:lint/$data
         * @type    array
         */
        protected $data = array();
        /**/

        /**
         * Prepare page
         *
         * @octdoc  m:lint/prepare
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
                $this->project = $args->getValue('p');
            } else {
                die("usage: ./lint.php -p project-name\n");
            }
        }

        /**
         * Validate parameters.
         *
         * @octdoc  m:lint/validate
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
         * @octdoc  m:lint/dialog
         * @param   string                          $action         Action that led to current page.
         */
        public function dialog($action)
        /**/
        {
        }

        /**
         * Get a file iterator for a specified directory and specified regular expression matching file names.
         *
         * @octdoc  m:lint/getIterator
         * @param   string                          $dir            Director to iterate recusrivly.
         * @param   string                          $regexp         Regular expression each file has to match to.
         * @param   string                          $exclude        Optional pattern for filtering files.
         * @return  \RegexIterator                                  The iterator.
         */
        protected function getIterator($dir, $regexp, $exclude = null)
        /**/
        {
            $iterator = new \RegexIterator(
                new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir)),
                $regexp,
                \RegexIterator::GET_MATCH
            );

            if (!is_null($exclude)) {
                $iterator = new \org\octris\core\type\filteriterator($iterator, function($current, $filename) use ($exclude) {
                    return !preg_match($exclude, $filename);
                });
            }

            return $iterator;
        }

        /**
         * Render.
         *
         * @octdoc  m:lint/render
         */
        public function render()
        /**/
        {
            if (!($work_dir = \org\octris\core\app\cli::getPath(\org\octris\core\app\cli::T_PATH_WORK, $this->project))) {
                die("unable to resolve work directory\n");
            }

            // lint php files
            $iterator = $this->getIterator($work_dir, '/\.php$/', '/(\/data\/cldr\/|\/tools\/project\/data\/skel\/)/');

            foreach ($iterator as $filename => $cur) {
                system('/usr/bin/env php -l ' . escapeshellarg($filename));
            }

            // lint templates
            if (($tpl_dir = \org\octris\core\app\cli::getPath(\org\octris\core\app\cli::T_PATH_WORK_TPL, $this->project)) !== false) {
                $iterator = $this->getIterator($tpl_dir, '/\.html$/');

                $tpl = new \org\octris\core\tpl\lint();

                foreach ($iterator as $filename => $cur) {
                    print $filename . "\n";

                    try {
                        $tpl->process($filename, \org\octris\core\tpl::T_ESC_HTML);
                    } catch(\Exception $e) {
                    }
                }
            }

            // lint skeleton in core framework
            if ($this->project == 'org.octris.core' &&
                ($tpl_dir = \org\octris\core\app\cli::getPath(\org\octris\core\app\cli::T_PATH_WORK, $this->project, 'tools/project/data/skel/')) !== false) {
                $iterator = $this->getIterator($tpl_dir, '/\.php$/');

                $tpl = new \org\octris\core\tpl\lint();

                foreach ($iterator as $filename => $cur) {
                    print $filename . "\n";

                    try {
                        $tpl->process($filename, \org\octris\core\tpl::T_ESC_AUTO);
                    } catch(\Exception $e) {
                    }
                }
            }

            print "done.\n";
        }
    }
}
