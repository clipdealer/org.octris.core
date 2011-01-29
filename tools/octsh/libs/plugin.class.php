<?php

namespace org\octris\core\octsh\libs {
    /**
     * Super-class for octris shell plugins. This class has to be inherited to 
     * allow a class to become an octris shell plugin.
     *
     * @octdoc      c:libs/plugin
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    abstract class plugin extends \org\octris\core\app\cli\page
    /**/
    {
        /**
         * Returns a short description of the plugin, intended for the help systems command
         * overview page.
         *
         * @octdoc  m:plugin/getInfo
         * @return  string                                      Short description of plugin.
         */
        // abstract public function getInfo();
        /**/

        /**
         * Returns long help description of the plugin.
         *
         * @octdoc  m:plugin/getHelp
         * @return  string                                      Long description of plugin.
         */
        // abstract public function getHelp();
        /**/
    }
}
