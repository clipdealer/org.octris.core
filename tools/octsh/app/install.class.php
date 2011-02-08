<?php

namespace org\octris\core\octsh\app {
    use \org\octris\core\app as app;
    use \org\octris\core\cli\stdio as stdio;
    use \org\octris\core\validate as validate;
    use \org\octris\core\provider as provider;
    
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
         * Constructor.
         *
         * @octdoc  m:install/__construct
         */
        public function __construct()
        /**/
        {
            parent::__construct();
            
            $this->addValidator(
                'request',
                'install', 
                array(
                    'type'              => validate::T_OBJECT,
                    'keyrename'         => array('project'),
                    'properties'        => array(
                        'project'       => array(
                            'type'       => validate::T_CHAIN,
                            'chain'      => array(
                                array(
                                    'type'      => validate::T_PROJECT,
                                    'invalid'   => 'project name is invalid',
                                ),
                                array(
                                    'type'      => validate::T_CALLBACK,
                                    'callback'  => function($project, $validator) {
                                        $work = app::getPath(app::T_PATH_WORK, $project);
                                    
                                        if (!($return = is_dir($work))) {
                                            $validator->addError('project does not exist');
                                        }

                                        return $return;
                                    }
                                )
                            )
                        )        
                    )
                )
            );
        }

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
            list($is_valid, $data, $errors) = $this->applyValidator('request', $action);
            
            if (!$is_valid) {
                $last_page->addErrors($errors);
                
                return $last_page;
            }

            if (!isset($data['project'])) {
                $state   = app::getInstance()->getState();
                $project = $state['project'];
            } else {
                $project = $data['project'];
            }
        
            $env  = provider::access('env');
            $base = $env->getValue('OCTRIS_BASE', validate::T_PATH);
            $work = app::getPath(app::T_PATH_WORK, $project);

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
         * Validate parameters.
         *
         * @octdoc  m:help/validate
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
         * @octdoc  m:install/dialog
         * @param   string                          $action         Action that led to current page.
         */
        public function dialog($action)
        /**/
        {
        }
    }
}
