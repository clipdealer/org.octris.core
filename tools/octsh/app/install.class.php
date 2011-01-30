<?php

namespace org\octris\core\octsh\app {
    use \org\octris\core\app as app;
    use \org\octris\core\cli\stdio as stdio;
    
    /**
     * Implements installer for applications based on octris framework.
     *
     * @octdoc      c:app/install
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class install extends \org\octris\core\octsh\libs\plugin
    /**/
    {
        /**
         * The entry points to which the current page should allow requests to have to be defined with this
         * property.
         *
         * @octdoc  v:install/$next_page
         * @var     array
         */
        protected $next_pages = array();
        /**/

        /**
         * Name of project to install.
         *
         * @octdoc  v:install/$project
         * @var     string
         */
        protected $project = '';
        /**/

        /**
         * Prepare page
         *
         * @octdoc  m:install/prepare
         * @param   \org\octris\core\app\page       $last_page      Instance of last called page.
         * @param   string                          $action         Action that led to current page.
         * @return  mixed                                           Returns either page to redirect to or null.
         */
        public function prepare(\org\octris\core\app\page $last_page, $action)
        /**/
        {
            $project = $this->project;
            $base    = $_ENV['OCTRIS_BASE']->value;
            
            // create directories
            if ($project == 'org.octris.core') {
                mkdir($base . '/cache',     0755, true);
                mkdir($base . '/data',      0755, true);
                mkdir($base . '/etc',       0755, true);
                mkdir($base . '/host',      0755, true);
                mkdir($base . '/libs',      0755, true);
                mkdir($base . '/locale',    0755, true);
                mkdir($base . '/log',       0755, true);
                mkdir($base . '/templates', 0755, true);
                mkdir($base . '/tools',     0755, true);
            } elseif (!is_dir($base . '/host')) {
                $last_page->addError("You have to install 'org.octris.core' first");
                
                return $last_page;
            } else {
                mkdir($base . '/host/' . $project,             0755, true);
                mkdir($base . '/host/' . $project . '/libsjs', 0777, true);
                mkdir($base . '/host/' . $project . '/styles', 0777, true);
            }
            
            mkdir($base . '/cache/' . $project . '/data',        0777, true);
            mkdir($base . '/cache/' . $project . '/templates_c', 0777, true);
            
            // create symlinks
            $work = app::getPath(app::T_PATH_WORK, $project);
            
            if (is_dir($work . '/data')) {
                symlink($work . '/data', $base . '/data/' . $project);
            }
            if (is_dir($work . '/etc')) {
                symlink($work . '/etc', $base . '/etc/' . $project);
            }
            if (is_dir($work . '/libs')) {
                symlink($work . '/libs', $base . '/libs/' . $project);
            }
            if (is_dir($work . '/locale')) {
                symlink($work . '/locale', $base . '/locale/' . $project);
            }
            if (is_dir($work . '/templates')) {
                symlink($work . '/templates', $base . '/templates/' . $project);
            }

            if ($project != 'org.octris.core') {
                if (is_file($work . '/host/index.php')) {
                    symlink($work . '/host/index.php',  $base . '/host/' . $project . '/index.php');
                }
                if (is_file($work . '/host/robots.txt')) {
                    symlink($work . '/host/robots.txt', $base . '/host/' . $project . '/robots.txt');
                }
                if (is_dir($work . '/host/error')) {
                    symlink($work . '/host/error',      $base . '/host/' . $project . '/error');
                }
                if (is_dir($work . '/host/resources')) {
                    symlink($work . '/host/resources',  $base . '/host/' . $project . '/resources');
                }
            }
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
            } elseif (is_array($project)) {
                $last_page->addError("usage: 'install [<path-of-project>]'");
            } else {
                $state = app::getInstance()->getState();
                
                $this->project = $state['project']->value;
            }
            
            return (count($last_page->errors) == 0
                    ? null
                    : $last_page);
        }

        /**
         * Implements dialog.
         *
         * @octdoc  m:install/dialog
         * @param   string                          $action         Action that led to current page.
         */
        public function dialog($action)
        /**/
        {
        }
    }
}
