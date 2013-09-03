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
     * Build a project.
     *
     * @octdoc      c:app/build
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class build extends \org\octris\core\app\cli\page
    /**/
    {
        /**
         * Prepare page
         *
         * @octdoc  m:build/prepare
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
         * @octdoc  m:build/validate
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
         * @octdoc  m:build/dialog
         * @param   string                          $action         Action that led to current page.
         */
        public function dialog($action)
        /**/
        {
        }

        /**
         * Render.
         *
         * @octdoc  m:build/render
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

            $recipe   = $work_dir . '/phar/recipe.yml';
            $builddir = $work_dir . '/build';

            if (is_readable($recipe) && ($tmp = yaml_parse_file($recipe)) && !is_null($tmp)) {
                $cfg = array_merge_recursive(array(
                    'meta' => array(), 'dependencies' => array('php' => array())
                ), $tmp);
            } else {
                die("project has no recipe or recipe is invalid\n");
            }

            if (!is_dir($builddir)) {
                if (!@mkdir($builddir)) {
                    die(sprintf("unable to create build directory '%s'\n", $builddir));
                }
            }

            // create phar
            $file = $builddir . '/' . $appname;

            if (file_exists($file)) {
                unlink($file);
            }
            if (file_exists($file . '.phar')) {
                unlink($file . '.phar');
            }

            $phar = new \Phar($file . '.phar', 0, $appname . '.phar');
            $phar->setStub(file_get_contents($work_dir . '/phar/stub.php'));
            $phar->setMetadata($cfg['meta']);

            // add application library
            $iterator = new \RegexIterator(
                new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($work_dir . '/libs/')),
                '/\.php$/',
                \RegexIterator::GET_MATCH
            );

            $len = strlen(rtrim($work_dir, '/'));
            foreach ($iterator as $filename => $cur) {
                $phar->addFile($filename, substr($filename, $len));
            }

            // add dependencies
            if (count($cfg['dependencies']['php']) > 0) {
                foreach ($cfg['dependencies']['php'] as $dependency) {
                    if (($filename = stream_resolve_include_path($dependency))) {
                        $phar->addFile($filename, 'deps/' . $dependency);
                    } else {
                        printf("warning: unable to locate dependency '%s'\n", $dependency);
                    }
                }
            }

            // remove phar extension and make file executable
            rename($file . '.phar', $file);
            chmod($file, 0755);
        }
    }
}
