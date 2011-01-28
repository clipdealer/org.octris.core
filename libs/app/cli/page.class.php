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
         * Implements render page of core page class. Make it final, because
         * render should not be used for cli application pages. Instead the 
         * abstract method 'dialog' must be implemented.
         *
         * @octdoc  m:page/render
         */
        public final function render()
        /**/
        {
        }
    }
}
