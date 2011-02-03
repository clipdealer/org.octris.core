<?php

namespace org\octris\core\octsh\app {
    use \org\octris\core\app as app;
    use \org\octris\core\validate as validate;
    
    /**
     * Help system for the shell.
     *
     * @octdoc      c:app/help
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class help extends \org\octris\core\octsh\libs\plugin
    /**/
    {
        /**
         * The entry points to which the current page should allow requests to have to be defined with this
         * property.
         *
         * @octdoc  v:help/$next_page
         * @var     array
         */
        protected $next_pages = array();
        /**/

        /**
         * Command to display help for.
         *
         * @octdoc  v:help/$command
         * @var     string
         */
        protected $command = '';
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:page/__construct
         */
        public function __construct()
        /**/
        {
            parent::__construct();
            
            $registry = \org\octris\core\registry::getInstance();

            $this->addValidator('help', $_REQUEST, array(
                'type'              => validate::T_OBJECT,
                'keyrename'         => array('command'),
                'properties'        => array(
                    'command'       => array(
                        'type'      => validate::T_CALLBACK,
                        'callback'  => function($command, $validator) use ($registry) {
                            print "test";
                            
                            if (!($return = isset($registry->commands))) {
                                $validator->addError('help is not available');
                            } elseif (!($return = isset($registry->commands[$command]))) {
                                $validator->addError("no help available for unknown command '$command'");
                            }

                            return $return;
                        }
                    )        
                )
            ));
             
            // 'project'       => array(
            //     'type'      => validate::T_CHAIN,
            //     'chain' => array(
            //         array(
            //             'type'      => validate::T_PROJECT,
            //             'invalid'   => 'Project name is invalid'
            //         ),
            //         array(
            //             'type'      => validate::T_CALLBACK,
            //             'options'   => array(
            //             'callback'  => function($value) {
            //                 print "$value\n";
            //                 return true;
            //             }
            //         )
            //     )
            // )
        }

        /**
         * Prepare page
         *
         * @octdoc  m:help/prepare
         * @param   \org\octris\core\app\page       $last_page      Instance of last called page.
         * @param   string                          $action         Action that led to current page.
         * @return  mixed                                           Returns either page to redirect to or null.
         */
        public function prepare(\org\octris\core\app\page $last_page, $action)
        /**/
        {
            if (($return = $this->applyValidator($action)) !== true) {
                $last_page->addErrors($return);
                
                return $last_page;
            }
            
            $this->command = $_REQUEST['command'];
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
         * @octdoc  m:help/dialog
         * @param   string                          $action         Action that led to current page.
         */
        public function dialog($action)
        /**/
        {
            $registry = \org\octris\core\registry::getInstance();
            
            if ($this->command == '') {
                print "List of all OCTRiS shell commands:\n\n";
                
                foreach ($registry->commands as $command => $class) {
                    printf("    %s\n", $command);
                }
                
                print "\n";
                print "Enter 'help <command>' for more information on a specific command.\n";
            } else {
                printf("Help for command '%s':\n\n", $this->command);
            }
        }
    }
}
