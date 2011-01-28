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
         * Abstract method definition.
         *
         * @octdoc  m:page/dialog
         */
        abstract public function dialog();
        /**/
        
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
