<?php

namespace org\octris\core\app\cli {
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
         * @octdoc  v:page/$next_page
         * @var     array
         */
        protected $next_page = array();
        /**/
        
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
         * Apply validation ruleset to specified parameters.
         *
         * @octdoc  m:page/validate
         * @param   \org\octris\core\app\cli\page   $last_page      Instance of last called page.
         * @param   string                          $action         Action to select ruleset for.
         * @param   array                           $parameters     Parameters to validate.
         * @return  \org\octris\core\app\cli\page                   Returns page to display errors for.
         */
        public function validate(\org\octris\core\app\cli\page $last_page, $action, array $parameters = array())
        /**/
        {
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
