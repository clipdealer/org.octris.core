<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\auth {
    /**
     * Interface for building identity storage handlers.
     *
     * @octdoc      i:auth/storage_if
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    interface storage_if {
        /**
         * Returns whether storage contains data or not.
         *
         * @octdoc  m:storage_if/isEmpty
         * @return                                  Returns true, if storage is empty.
         */
        public function isEmpty();
        /**/

        /**
         * Store data in storage.
         *
         * @octdoc  m:storage_if/setData
         * @param   array           $data           Data to store in storage.
         */
        public function setData(array $data);
        /**/

        /**
         * Return data from storage.
         *
         * @octdoc  m:storage_if/getData
         * @return  array                           Data stored in storage.
         */
        public function getData();
        /**/

        /**
         * Deletes data from storage.
         *
         * @octdoc  m:storage_if/unsetData
         */
        public function unsetData();
        /**/
    }
}
