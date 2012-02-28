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
     * Database devices base class.
     *
     * @octdoc      c:db/device
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    abstract class device {
        /**
         * Pool that handles this connection.
         *
         * @octdoc  p:device/$pool
         * @var     \org\octris\core\db|null 
         */
        protected $pool = null;
        /**/

        /**
         * Whether the connection was released to the pool.
         *
         * @octdoc  p:device/$released
         * @var     bool
         */
        protected $released = false;
        /**/

        /**
         * Set pool for connection.
         * 
         * @octdoc  m:device/setPool
         * @param   \org\octris\core\db         $pool           Pool to handle connection with.
         */
        public function setPool(\org\octris\core\db $pool)
        {
            if (!is_null($this->pool)) {
                throw new \Exception('connection is already assigned to a pool');
            } else {
                $this->pool = $pool;
            }
        }

        /**
         * Release connection, hand connection over to pool.
         *
         * @octdoc  m:device/release
         */
        public function release()
        /**/
        {
            if (!is_null($this->pool)) {
                $this->pool->release($this);

                $this->released = true;
            }
        }

		/**
		 * Create database connection.
		 *
		 * @octdoc 	m:device/getConnection
		 * @return 	\org\octris\core\db\mongodb\connection 			Connection to a MongoDB database.
		 */
		abstract public getConnection();
		/**/
    }
}
