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
         * Pool that handles this connection.
         *
         * @octdoc  p:pool_tr/$pool
         * @var     \org\octris\core\db|null 
         */
        protected $pool = null;
        /**/

        /**
         * Whether the connection was released to the pool.
         *
         * @octdoc  p:pool_tr/$released
         * @var     bool
         */
        protected $released = false;
        /**/

        /**
         * Set pool for connection.
         * 
         * @octdoc  m:pool_tr/setPool
         * @param   \org\octris\core\db\pool        $pool           Pool to set for connection.
         */
        public function setPool(\org\octris\core\db\pool $pool)
        {
            $this->pool = $pool;
        }

        /**
         * Release connection, hand connection over to pool.
         *
         * @octdoc  m:pool_tr/release
         */
        public function release()
        /**/
        {
            if (!is_null($this->pool)) {
                $this->pool->release($this);

                $this->released = true;
            }
        }
    }
}
