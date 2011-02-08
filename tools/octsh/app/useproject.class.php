<?php

namespace org\octris\core\octsh\app {
    use \org\octris\core\app as app;
    use \org\octris\core\validate as validate;
    use \org\octris\core\provider as provider;
    
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
         * Constructor.
         *
         * @octdoc  m:useproject/__construct
         * @param   
         */
        public function __construct()
        /**/
        {
            parent::__construct();
            
            $this->addValidator(
                'request',
                'use', 
                array(
                    'type'              => validate::T_OBJECT,
                    'keyrename'         => array('project'),
                    'properties'        => array(
                        'project'       => array(
                            'type'      => validate::T_CHAIN,
                            'chain'     => array(
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
                            ),
                            'required'  => 'usage: use <path-of-project>'
                        )        
                    )
                )
            );
        }

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
            list($is_valid, $data, $errors) = $this->applyValidator('request', $action);
            
            if (!$is_valid) {
                $last_page->addErrors($errors);
                
                return $last_page;
            }
            
            $state = app::getInstance()->getState();
            $state['project'] = $data['project'];
            
            return $last_page;
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
         * @octdoc  m:useproject/dialog
         * @param   string                          $action         Action that led to current page.
         */
        public function dialog($action)
        /**/
        {
        }
    }
}
