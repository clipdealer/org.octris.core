<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\project\libs {
    /**
     * Tools for xgettext, main class.
     *
     * @octdoc      c:libs/xgettext
     * @copyright   copyright (c) 2014 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class xgettext 
    /**/
    {
        /**
         * Instance of xgettext library.
         *
         * @octdoc  p:xgettext/$instance
         * @type    \org\octris\core\project\libs\xgettext|null
         */
        protected static $instance = null;
        /**/

        /**
         * Temporary directory.
         *
         * @octdoc  p:xgettext/$tmp
         * @type    string
         */
        protected $tmp = '';
        /**/

        /**
         * Loaded plugins.
         *
         * @octdoc  p:xgettext/$plugins
         * @type    array
         */
        protected $plugins = array();
        /**/
        
        /**
         * Constructor.
         *
         * @octdoc  m:xgettext/__construct
         */
        protected function __construct()
        /**/
        {
        }

        /**
         * Get instance of xgettext class.
         *
         * @octdoc  m:xgettext/getInstance
         */
        public function getInstance()
        /**/
        {
            if (is_null(self::$instance)) {
                self::$instance = new static();
            }
        
            return self::$instance;
        }
    
        /**
         * Create temporary directory.
         *
         * @octdoc  m:xgettext/getTempDir
         * @return  string                                  Path of temporary directory.
         */
        public function getTempDir()
        /**/
        {
            if (is_null($this->tmp)) {
                $this->tmp = '/tmp/' . sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                    mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
                    mt_rand( 0, 0x0fff ) | 0x4000,
                    mt_rand( 0, 0x3fff ) | 0x8000,
                    mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
                );
        
                mkdir($this->tmp);
            }
        
            return $this->tmp;
        }
    
        /**
         * Fix path names in gettext files.
         *
         * @octdoc  m:xgettext/fixPaths
         * @param   string              $msg                Message.
         * @param   string              $project            Name of project.
         */
        public function fixPaths()
        /**/
        {
        }
    }
}