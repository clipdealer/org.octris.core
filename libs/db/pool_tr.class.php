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
     * Implements functionality required for using a connection pool.
     *
     * @octdoc      t:db/pool_tr
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    trait pool_tr
    /**/
    {
        /**
         * Type of pool the device is related to.
         *
         * @octdoc  p:device/$type
         * @var     string
         */
        protected $type = '';
        /**/

        /**
         * Pool that handles this connection.
         *
         * @octdoc  p:pool_tr/$pool
         * @var     \org\octris\core\db|null 
         */
        protected $pool = null;
        /**/

        /**
         * Set pool for connection, to be called from constructor.
         *
         * @octdoc  m:pool_tr/setPool
         * @param   string                              $type       Type of pool, 'master' or 'slave'.
         * @param   \org\octris\core\db                 $pool       Pool to assigned to connection.
         */
        public function setPool($type, \org\octris\core\db $pool)
        /**/
        {
            $this->type = $type;
            $this->pool = $pool;   
        }

        /**
         * Get instance of connection pool.
         * 
         * @octdoc  m:pool_tr/setPool
         * @return  \org\octris\core\db                             Pool assigned to connection.
         */
        public function getPool()
        /**/
        {
            return $this->pool;
        }

        /**
         * Release connection, hand connection over to pool.
         *
         * @octdoc  m:pool_tr/release
         */
        public function release()
        /**/
        {
            $this->pool->release($this->type, $this);
        }
    }
}
