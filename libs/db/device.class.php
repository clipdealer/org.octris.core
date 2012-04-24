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
        private $pool = null;
        /**/

        /**
         * Whether the connection was released to the pool.
         *
         * @octdoc  p:device/$released
         * @var     bool
         */
        private $released = false;
        /**/

        /**
         * Set pool for connection.
         * 
         * @octdoc  m:device/setPool
         * @param   string                      $type           Type of pool.
         * @param   \org\octris\core\db\pool    $pool           Pool to handle connection with.
         */
        public function setPool($type, \org\octris\core\db\pool $pool)
        {
            if (!is_null($this->pool)) {
                throw new \Exception('connection is already assigned to a pool');
            } else {
                $this->type = $type;
                $this->pool = $pool;
            }
        }

		/**
		 * Create database connection.
		 *
		 * @octdoc 	m:device/getConnection
		 * @return 	\org\octris\core\db\device\onnection_if 	Connection to a database.
		 */
		abstract public function getConnection();
		/**/
    }
}
