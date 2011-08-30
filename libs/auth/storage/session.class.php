<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\auth\storage {
    /**
     * Storage handler for storing identity into session.
     *
     * @octdoc      c:storage/session
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class session implements \org\octris\core\auth\storage_if
    /**/
    {
        /**
         * Instance of session class.
         *
         * @octdoc  v:session/$session
         * @var     \org\octris\core\app\web\session
         */
        protected $session;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:session/__construct
         */
        public function __construct()
        /**/
        {
            $this->session = \org\octris\core\app\web\session::getInstance();
        }

        /**
         * Returns whether storage contains data or not.
         *
         * @octdoc  m:storage/isEmpty
         * @return                                  Returns true, if storage is empty.
         */
        public function isEmpty()
        /**/
        {
            return (!$this->session->isExist('auth.storage'));
        }

        /**
         * Store data in storage.
         *
         * @octdoc  m:storage/setData
         * @param   array           $data           Data to store in storage.
         */
        public function setData(array $data)
        /**/
        {
            $this->data = $this->session->setValue('auth.storage', $data);
        }

        /**
         * Return data from storage.
         *
         * @octdoc  m:storage_if/getData
         * @return  array                           Data stored in storage.
         */
        public function getData()
        /**/
        {
            return $this->data = $this->session->getValue('auth.storage');
        }

        /**
         * Deletes data from storage.
         *
         * @octdoc  m:storage/unsetData
         */
        public function unsetData()
        /**/
        {
            $this->session->unsetValue('auth.storage');
        }
    }
}
