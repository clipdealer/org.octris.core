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
     *  a project.
     *
     * @octdoc      c:app/getdeps
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class getdeps extends \org\octris\core\app\cli\page
    /**/
    {
        /**
         * Autoloader class name.
         *
         * @octdoc  p:getdeps/$autoloader
         * @var     string
         */
        protected $autoloader = '';
        /**/

        /**
         * Prepare page
         *
         * @octdoc  m:getdeps/prepare
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
                die("usage: ./getdeps.php -p project-name\n");
            }
        }

        /**
         * Validate parameters.
         *
         * @octdoc  m:getdeps/validate
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
         * @octdoc  m:getdeps/dialog
         * @param   string                          $action         Action that led to current page.
         */
        public function dialog($action)
        /**/
        {
        }

        /**
         * Get a file iterator for a specified directory and specified regular expression matching file names.
         *
         * @octdoc  m:getdeps/getIterator
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
         * Dependency resolver.
         *
         * @octdoc  m:getdeps/resolver
         * @param   \Iterator           $iterator               An iterator for iterating files.
         * @return  array                                       Determined dependencies.
         */
        protected function resolver(\Iterator $iterator)
        /**/
        {
            $autoloader = $this->autoloader;
            $resolved   = array();
            $resolve    = array_keys(iterator_to_array($iterator));
            
            $resolver = function(array $resolve) use (&$resolver, &$resolved, $autoloader) {
                $dependencies = array();

                while (($filename = array_shift($resolve))) {
                    $resolved[] = $filename;

                    if (preg_match_all(
                            '|/\*\*\s*\n(?:\s*\*.*\n)+\s*\*/\s*\n|', 
                            file_get_contents($filename),
                            $comments
                        )) {

                        foreach ($comments[0] as $comment) {
                            if (preg_match_all('/^\s*\*\s+@depends\s+([^\s]+)\s*$/m', $comment, $match)) {
                                foreach ($match[1] as $dependency) {
                                    $filename = stream_resolve_include_path(
                                        $autoloader::resolve($dependency)
                                    );

                                    if (!in_array($filename, $resolved) && !in_array($filename, $resolve)) {
                                        $dependencies[] = $dependency;                               
                                        $resolve[]      = $filename;
                                    }
                                }
                            }
                        }
                    }
                }

                return $dependencies;
            };

            return $resolver($resolve);
        }

        /**
         * Render.
         *
         * @octdoc  m:getdeps/render
         */
        public function render()
        /**/
        {
            if (!($work_dir = \org\octris\core\app\cli::getPath(\org\octris\core\app\cli::T_PATH_WORK, $this->project))) {
                die("unable to resolve work directory\n");
            }

            $appname  = \org\octris\core\app\cli::getAppName($this->project);
            $launcher = $work_dir . '/' . $appname . '.php';

            if ((!is_executable($launcher))) {
                die("no cli project\n");
            }

            // import autoloader of cli application
            require_once($work_dir . '/libs/autoloader.class.php');
            $this->autoloader = '\\' . preg_replace('|\.|', '\\', $this->project, 2) . '\\libs\\autoloader';

            // dependency resolver
            $iterator = $this->getIterator(
                $work_dir, 
                '/\.php$/', 
                '/(\/data\/cldr\/|\/tools\/project\/data\/skel\/|\/phar\/)/'
            );

            $deps = $this->resolver($iterator);

            if (($cnt = count($deps)) == 0) {
                print "no dependencies found\n";
            } else {
                printf("found %d dependenc%s:\n\n", $cnt, ($cnt == 1 ? 'y' : 'ies'));

                print implode("\n", $deps) . "\n\n";
            }
        }
    }
}
