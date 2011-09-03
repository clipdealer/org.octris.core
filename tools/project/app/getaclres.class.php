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
     * Create/update ACL resource list of a project.
     *
     * @octdoc      c:app/getaclres
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class getaclres extends \org\octris\core\app\cli\page
    /**/
    {
        /**
         * Name of project
         *
         * @octdoc  v:getaclres/$project
         * @var     string
         */
        protected $project;
        /**/

        /**
         * Path to the app folder of the project.
         *
         * @octdoc  v:getaclres/$app_path
         * @var     string
         */
        protected $app_path;
        /**/

        /**
         * Prepare page
         *
         * @octdoc  m:getaclres/prepare
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
                $this->app_path = \org\octris\core\app::getPath(\org\octris\core\app::T_PATH_LIBS, $project) . '/app';

                if (!is_dir($this->app_path) || !is_file($this->app_path . '/entry.class.php')) {
                    die(sprintf("'%s' does not seem to be an application created with the OCTRiS framework", $project));
                }

                $this->project = $project;
            } else {
                die("usage: ./getaclres.php -p project-name\n");
            }
        }

        /**
         * Validate parameters.
         *
         * @octdoc  m:getaclres/validate
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
         * @octdoc  m:getaclres/dialog
         * @param   string                          $action         Action that led to current page.
         */
        public function dialog($action)
        /**/
        {
        }

        /**
         * Render.
         *
         * @octdoc  m:getaclres/render
         */
        public function render()
        /**/
        {
            $acl = new \org\octris\core\auth\acl();
            $res = array();

            $analyze = function($page, $module) use (&$analyze, &$res) {
                static $processed = array();
                static $_module   = '';

                if ($module != $_module) {
                    $processed[] = $_module = $module;

                    $res[$module] = array(
                        'name'    => $module,
                        'actions' => array()
                    );
                }

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

                $res[$page] = array(
                    'name'    => $page,
                    'actions' => array_keys($pages)
                );

                // process next_pages
                foreach ($pages as $k => $v) {
                    $analyze("\\$v", '\\' . ltrim(substr($v, 0, strpos($v, '\\app\\') + 5)), '\\');
                }
            };

            $module = '\\' . str_replace('.', '\\', $this->project) . '\\app';
            $entry  = $module . '\\entry';

            $analyze($entry, $this->app);

            ksort($res);

            foreach ($res as $k => $v) {
                $acl->addResource($v['name'], $v['actions']);
            }


        }
    }
}
