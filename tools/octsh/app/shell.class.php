<?php

namespace org\octris\core\octsh\app {
    use \org\octris\core\app as app;
    
    /**
     * Implements command line shell.
     *
     * @octdoc      c:app/shell
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class shell extends \org\octris\core\app\cli\page
    /**/
    {
        /**
         * The entry points to which the current page should allow requests to have to be defined with this
         * property.
         *
         * @octdoc  v:shell/$next_page
         * @var     array
         */
        protected $next_pages = array(
            'quit'  => '\org\octris\core\octsh\app\quit'
        );
        /**/

        /**
         * Prepare page.
         *
         * @octdoc  m:shell/prepare
         * @param   \org\octris\core\app\page       $last_page      Instance of last called page.
         * @param   string                          $action         Action that led to current page.
         * @return  mixed                                           Returns either page to redirect to or null.
         */
        public function prepare(\org\octris\core\app\page $last_page, $action)
        /**/
        {
        }

        /**
         * Render page.
         *
         * @octdoc  m:entry/render
         * @param   string                          $action         Action that led to current page.
         * @return  string                                          Action that was triggered by the dialog.
         */
        public function dialog($action)
        /**/
        {
            $readline = \org\octris\core\app\cli\readline::getInstance('/tmp/octsh.txt');
            $prompt   = 'octsh> ';

            do {
                $return = trim($readline->readline($prompt));
            } while ($return == '');

            $args = explode(' ', $return);
            $cmd  = array_shift($args);

            if (!isset($this->next_pages[$cmd])) {
                $cmd = 'error';
            }

            return $cmd;
        }
    }
}
