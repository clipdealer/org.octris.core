<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\db {
    /**
     * Interface for implementing connection pool functionality. Note, that a connection handler has
     * to implement this interface, but only needs to make use of the trait "pool_tr" to have all
     * required functionality implemented.
     *
     * @octdoc      i:db/pool_if
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    interface pool_if 
    /**/
    {
        /**
         * Set pool for connection, to be called from constructor.
         *
         * @octdoc  m:pool_if/setPool
         * @param   string                              $type       Type of pool, 'master' or 'slave'.
         * @param   \org\octris\core\db                 $pool       Pool to assigned to connection.
         */
        public function setPool($type, \org\octris\core\db $pool);
        /**/

        /**
         * Get instance of connection pool.
         * 
         * @octdoc  m:pool_if/setPool
         * @return  \org\octris\core\db                             Pool assigned to connection.
         */
        public function getPool();
        /**/

        /**
         * Release connection, hand connection over to pool.
         *
         * @octdoc  m:pool_if/release
         */
        public function release();
        /**/
    }
}
