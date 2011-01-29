<?php

namespace org\octris\core\octsh\app {
    use \org\octris\core\app as app;
    
    /**
     * Switch to other project.
     *
     * @octdoc      c:app/useproject
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class useproject extends \org\octris\core\octsh\libs\plugin
    /**/
    {
        /**
         * The entry points to which the current page should allow requests to have to be defined with this
         * property.
         *
         * @octdoc  v:useproject/$next_page
         * @var     array
         */
        protected $next_pages = array();
        /**/

        /**
         * Name of project to install.
         *
         * @octdoc  v:useproject/$project
         * @var     string
         */
        protected $project = '';
        /**/

        /**
         * Prepare page
         *
         * @octdoc  m:useproject/prepare
         * @param   \org\octris\core\app\page       $last_page      Instance of last called page.
         * @param   string                          $action         Action that led to current page.
         * @return  mixed                                           Returns either page to redirect to or null.
         */
        public function prepare(\org\octris\core\app\page $last_page, $action)
        /**/
        {
            $state = app::getInstance()->getState();
            $state['project']->value = $this->project;
            
            return $last_page;
        }

        /**
         * Validate help parameters.
         *
         * @octdoc  m:help/validate
         * @param   \org\octris\core\app\cli\page   $last_page      Instance of last called page.
         * @param   string                          $action         Action to select ruleset for.
         * @param   array                           $parameters     Parameters to validate.
         * @return  \org\octris\core\app\cli\page                   Returns page to display errors for.
         */
        public function validate(\org\octris\core\app\cli\page $last_page, $action, array $parameters = array())
        /**/
        {
            $project = array_shift($parameters);
            
            if (is_scalar($project)) {
                if (!is_dir(app::getPath(app::T_PATH_WORK, $project))) {
                    $last_page->addError("unable to locate project '$project'");
                } else {
                    $this->project = $project;
                }
            } else {
                $last_page->addError("usage: 'use <path-of-project>'");
            }
            
            return (count($last_page->errors) == 0
                    ? null
                    : $last_page);
        }

        /**
         * Implements dialog.
         *
         * @octdoc  m:useproject/dialog
         * @param   string                          $action         Action that led to current page.
         */
        public function dialog($action)
        /**/
        {
        }
    }
}
