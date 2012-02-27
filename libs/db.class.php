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
	 * Core database class. 
	 *
	 * @octdoc		c:core/db
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
	class db 
	/**/
	{
		/**
		 * Types of database connections.
		 *
		 * @octdoc	d:db/T_DB_MASTER, T_DB_SLAVE
		 */
		const T_DB_MASTER = 'master';
		const T_DB_SLAVE  = 'slave';
		/*
		
		/**
		 * Name of database device.
		 *
		 * @octdoc 	p:db/$device_name
		 * @var 	string
		 */
		protected $device_name;
		/**/

		/**
		 * Stores database master device.
		 * 
		 * @octdoc	p:db/$master
		 * @var 	\org\octris\core\db\device_if
		 */
		protected $master;
		/**/

		/**
		 * Stores database slave devices
		 *
		 * @octdoc 	p:db/$slaves
		 * @var 	array
		 */
		protected $slaves = array();
		/**/

		/**
		 * Connection pool stores free database connections.
		 *
		 * @octdoc 	p:db/$pool
		 * @var 	array
		 */
		protected $pool = array();
		/**/

		/**
		 * Constructor creates a new pool and initializes it with a database device, which will be used as master
		 * connection to a database.
		 *
		 * @octdoc	m:db/__construct
		 */
		public function __construct(\org\octris\core\db\device_if $device)
		/**/
		{
			$this->slaves[] = $this->master = $device;
			$this->device_name = get_class($device);
		}

		/**
		 * Add a database device for a slave connection to the datbase.
		 *
		 * @octdoc	m:db/addSlave
		 */
		public function addSlave(\org\octris\core\db\device_if $device)
		/**/
		{
			if (!($device instanceof $this->device_name)) {
				throw new \Exception('master and slaves must be of the same device');
			} else {
				$this->slaves[] = $device;
			}
		}

		/**
		 * Return a connection of specified type.
		 *
		 * @octdoc	m:db/getConnection
		 * @param 	string 			$type 				Connection type ('master' or 'slave', db::T_DB_MASTER or db::T_DB_SLAVE).
		 * @Return 	\org\octris\core\db\connection 		Connection.
		 */
		public function getConnection($type)
		/**/
		{
			if ($type != \org\octris\core\db::T_DB_MASTER && $type != \org\octris\core\db::T_DB_SLAVE) {
				throw new \Exception('unknown connection type "' . $type . '"');
			} else {
				if (!($cn = array_shift($this->pool[$type]))) {
					// no more connections in the pool, create new one
					if ($type == \org\octris\core\db::T_DB_MASTER) {
						$device = $this->master;
					} else {
						shuffle($this->slaves);

						$device = $this->slaves[0];
					}

					$cn = $device->getConnection($this);
				}				
			}

			return $cn;
		}
	}
}
