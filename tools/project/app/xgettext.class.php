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
     * Implements xgettext wrapper.
     *
     * @octdoc      c:app/xgettext
     * @copyright   copyright (c) 2014 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class xgettext extends \org\octris\core\app\cli\page
    /**/
    {
        /**
         * Application data.
         *
         * @octdoc  p:xgettext/$data
         * @type    array
         */
        protected $data = array();
        /**/

        /**
         * Prepare page
         *
         * @octdoc  m:xgettext/prepare
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
                die("usage: ./xgettext.php -p project-name\n");
            }
        }

        /**
         * Validate parameters.
         *
         * @octdoc  m:xgettext/validate
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
         * @octdoc  m:xgettext/dialog
         * @param   string                          $action         Action that led to current page.
         */
        public function dialog($action)
        /**/
        {
        }

        /**
         * Get a file iterator for a specified directory and specified regular expression matching file names.
         *
         * @octdoc  m:xgettext/getIterator
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
         * @octdoc  m:xgettext/render
         */
        public function render()
        /**/
        {
            if (!($work_dir = \org\octris\core\app\cli::getPath(\org\octris\core\app\cli::T_PATH_WORK, $this->project))) {
                die("unable to resolve work directory\n");
            }

            print "done.\n";
        }
    }
}
