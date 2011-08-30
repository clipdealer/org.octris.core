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
     * Non persistent storage of identity. This is the default authentication
     * storage handler.
     *
     * @octdoc      c:storage/transient
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class transient implements \org\octris\core\auth\storage_if
    /**/
    {
        /**
         * Transient data storage.
         *
         * @octdoc  v:transient/$data
         * @var     array|null
         */
        protected $data = null;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:transient/__construct
         */
        public function __construct()
        /**/
        {
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
            return (empty($this->data));
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
            $this->data = $data;
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
            return $this->data;
        }

        /**
         * Deletes data from storage.
         *
         * @octdoc  m:storage/unsetData
         */
        public function unsetData()
        /**/
        {
            $this->data = null;
        }
    }
}
