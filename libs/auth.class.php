<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core {
    /**
     * Authentication and authorization library.
     *
     * @octdoc      c:core/auth
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class auth
    /**/
    {
        /**
         * Instance of auth library.
         *
         * @octdoc  v:auth/$instance
         * @var     \org\octris\core\auth
         */
        private static $instance = null;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:auth/__construct
         */
        protected function __construct()
        /**/
        {
        }

        /*
         * prevent cloning
         */
        private function __clone() {}

        /**
         * Return instance of auth class, implemented as singleton-pattern.
         *
         * @octdoc  m:auth/getInstance
         * @return  \org\octris\core\auth
         */
        public static function getInstance()
        /**/
        {
            if (is_null(self::$instance)) {
                self::$instance = new static();
            }

            return self::$instance;
        }

        /**
         * Sets the storage handler for authentication information.
         *
         * @octdoc  m:auth/setStorage
         * @param   \org\octris\core\auth\storage   $storage        Instance of storage backend.
         */
        public static function setStorage()
        /**/
        {

        }

        /**
         * Authenticate againat the specified authentication adapter.
         *
         * @octdoc  m:auth/authenticate
         * @param   \org\octris\core\auth\adapter   $adapter        Instance of adapter to use for authentication.
         */
        public function authenticate(\org\octris\core\auth\adapter $adapter)
        /**/
        {

        }

    }
}
