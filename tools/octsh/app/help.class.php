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
                        'options'   => array(
                            'callback'  => function($command) use ($registry) {
                                if (!isset($registry->commands)) {
                                    $last_page->addError('help is not available');
                                } elseif (!isset($registry->commands[$command])) {
                                    $last_page->addError("no help for unknown command '$command' available");
                                }

                                print "$value\n";
                                return true;
                            }
                        )
                    )        
                )
            ));
             
                    // 'project'       => array(
                    //     'type'      => validate::T_CHAIN,
                    //     'options'   => array(
                    //         'chain' => array(
                    //             array(
                    //                 'type'      => validate::T_PROJECT,
                    //                 'invalid'   => 'Project name is invalid'
                    //             ),
                    //             array(
                    //                 'type'      => validate::T_CALLBACK,
                    //                 'options'   => array(
                    //                     'callback'  => function($value) {
                    //                         print "$value\n";
                    //                         return true;
                    //                     }
                    //                 )
                    //                 'invalid'   => 'Project does not exist'
                    //             )
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
            return ($this->validate()
                    ? null
                    : $last_page);
            // $command  = array_shift($parameters);
            // 
            // if (is_scalar($command)) {
            // } elseif (is_array($command)) {
            //     $last_page->addError("usage: 'help' or 'help <command>'");
            // }
            // 
            return (count($last_page->errors) == 0
                    ? null
                    : $last_page);
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
