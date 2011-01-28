<?php

namespace org\octris\core\app\cli {
    /**
     * Page controller for cli mvc framework.
     *
     * @octdoc      c:cli/page
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    abstract class page
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
    }
}
