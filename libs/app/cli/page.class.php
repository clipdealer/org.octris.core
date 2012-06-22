<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\app\cli {
    use \org\octris\core\validate as validate;
    use \org\octris\core\provider as provider;
    
    /**
     * Page controller for cli mvc framework.
     *
     * @octdoc      c:cli/page
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    abstract class page extends \org\octris\core\app\page
    /**/
    {
        /**
         * Next valid actions.
         *
         * @octdoc  p:page/$next_page
         * @var     array
         */
        protected $next_page = array();
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
        }
        
        /**
         * Display and purge error messages.
         *
         * @octdoc  m:page/showErrors
         */
        public function showErrors()
        /**/
        {
            if (count($this->errors > 0)) {
                foreach ($this->errors as $error) {
                    printf("ERROR: %s\n", $error);
                }
                $this->errors = array();
            }
        }
        
        /**
         * Display and purge notification messages.
         *
         * @octdoc  m:page/showMessages
         */
        public function showMessages()
        /**/
        {
            if (count($this->messages > 0)) {
                foreach ($this->messages as $message) {
                    printf("NOTE: %s\n", $message);
                }
                $this->message = array();
            }
        }
        
        /**
         * Abstract method definition. CLI application dialog.
         *
         * @octdoc  m:page/dialog
         * @param   string          $action             Action that led to the dialog.
         * @return  array                               Array with triggered command and command parameters.
         * @abstract
         */
        abstract public function dialog($action);
        /**/
        
        /**
         * Apply validation ruleset.
         *
         * @octdoc  m:page/validate
         * @param   \org\octris\core\app\cli\page   $last_page      Instance of last called page.
         * @param   string                          $action         Action to select ruleset for.
         * @return  \org\octris\core\app\cli\page                   Returns page to display errors for.
         */
        public function validate($action)
        /**/
        {
        }

        /**
         * Determine the action of the request.
         *
         * @octdoc  m:page/getAction
         * @return  string                                      Name of action
         */
        public function getAction()
        /**/
        {
            $request = provider::access('request');
            
            if (!($action = $request->getValue('ACTION', validate::T_ALPHANUM))) {
                // try to determine action from a request parameter named ACTION_...
                foreach ($request->getPrefixed('ACTION_', validate::T_ALPHANUM) as $k => $v) {
                    $action = substr($k, 7);
                    break;
                }
            }

            if (!$action) {
                $action = '';
            }

            return $action;
        }

        /**
         * Implements render page of core page class, because this method is
         * optional for cli application pages. Instead the abstract method 
         * 'dialog' is required to be implemented for any cli application page.
         *
         * @octdoc  m:page/render
         */
        public function render()
        /**/
        {
        }
    }
}
