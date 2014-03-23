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
     * Create/update ACL configuration of a project.
     *
     * @octdoc      c:app/updateacl
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class updateacl extends \org\octris\core\app\cli\page
    /**/
    {
        /**
         * Name of project
         *
         * @octdoc  v:updateacl/$project
         * @type    string
         */
        protected $project;
        /**/

        /**
         * Path to the app folder of the project.
         *
         * @octdoc  v:updateacl/$app_path
         * @type    string
         */
        protected $app_path;
        /**/

        /**
         * Prepare page
         *
         * @octdoc  m:updateacl/prepare
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

                $this->project = $project;
            } else {
                die("usage: ./updateacl.php -p project-name\n");
            }
        }

        /**
         * Validate parameters.
         *
         * @octdoc  m:updateacl/validate
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
         * @octdoc  m:updateacl/dialog
         * @param   string                          $action         Action that led to current page.
         */
        public function dialog($action)
        /**/
        {
        }

        /**
         * Render.
         *
         * @octdoc  m:updateacl/render
         */
        public function render()
        /**/
        {
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

            // build/update ACL configuration
            $old_name = '/Users/harald/Projects/work/org.octris.core/etc/acl.yml.dist';

            $acl    = \org\octris\core\project\libs\acl::load($old_name);
            $status = $acl->merge($res);

            // status output
            print "updateacl status\n";
            print str_repeat('=', 22) . "\n";
            printf("deleted resources: % 3d\n", $status['del_resources']);
            printf("added resources  : % 3d\n", $status['add_resources']);
            printf("processed roles  : % 3d\n", $status['prc_roles']);
            printf("deleted policies : % 3d\n", $status['del_policies']);
            print str_repeat('-', 22) . "\n";
            printf("total resources  : % 3d\n", $status['tot_resources']);
            printf("total roles      : % 3d\n", $status['tot_roles']);
            printf("total policies   : % 3d\n\n", $status['tot_policies']);

            //
            do {
                $key = strtolower(\org\octris\core\app\cli\stdio::getPrompt('(v)iew, (s)ave, (a)bort [%s]? ', 'v'));
            } while ($key != 'v' && $key != 's' && $key != 'a');

            switch ($key) {
            case 'v':
                $new_name = '/tmp/octris.acl.yml.' . posix_getpid();

                $acl->save($new_name);

                pclose(popen(sprintf(
                    'opendiff %s %s -merge %s',
                    escapeshellarg($old_name),
                    escapeshellarg($new_name),
                    escapeshellarg($old_name)
                ), 'r'));
                break;
            case 's':
                $acl->save($old_name);
                break;
            case 'a':
                exit;
            }
        }
    }
}
