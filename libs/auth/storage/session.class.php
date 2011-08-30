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
         * Returns whether storage contains an identity or not.
         *
         * @octdoc  m:storage/isEmpty
         * @return                                                  Returns true, if storage is empty.
         */
        public function isEmpty()
        /**/
        {
            return (!$this->session->isExist('identity', __CLASS__));
        }

        /**
         * Store identity in storage.
         *
         * @octdoc  m:storage_if/setIdentity
         * @param   \org\octris\core\auth\identity  $identity       Identity to store in storage.
         */
        public function setIdentity(\org\octris\core\auth\identity $identity)
        /**/
        {
            $this->session->setValue('identity', $identity, __CLASS__);
        }

        /**
         * Return identity from storage.
         *
         * @octdoc  m:storage_if/getIdentity
         * @return  \org\octris\core\auth\identity                  Identity stored in storage.
         */
        public function getIdentity()
        /**/
        {
            return $this->session->getValue('identity', __CLASS__);
        }

        /**
         * Deletes identity from storage.
         *
         * @octdoc  m:storage/unsetIdentity
         */
        public function unsetIdentity()
        /**/
        {
            $this->session->unsetValue('identity', __CLASS__);
        }
    }
}
