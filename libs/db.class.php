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
		protected $pool = array(
			'master' => array(),
			'slave'  => array()
		);
		/**/

		/**
		 * Constructor creates a new pool and initializes it with a database device, which will be used as master
		 * connection to a database.
		 *
		 * @octdoc	m:db/__construct
		 * @param 	\org\octris\core\db\device 		$master 			Database device to set as master connection.
		 */
		public function __construct(\org\octris\core\db\device $master)
		/**/
		{
			$master->setPool(self::T_DB_MASTER, $this);

			$this->slaves[] = $this->master = $master;
			$this->device_name = get_class($master);
		}

		/**
		 * Add a database device for a slave connection to the datbase.
		 *
		 * @octdoc	m:db/addSlave
		 * @param 	\org\octris\core\db\device 		$slave 				Database device to set as slave connection.
		 */
		public function addSlave(\org\octris\core\db\device $slave)
		/**/
		{
			$slave->setPool(self::T_DB_SLAVE, $this);

			if (!($slave instanceof $this->device_name)) {
				throw new \Exception('master and slaves must be of the same device');
			} else {
				$this->slaves[] = $slave;
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
						// there's only one master host
						$device = $this->master;
					} else {
						// pick a random slave host
						shuffle($this->slaves);

						$device = $this->slaves[0];
					}

					$cn = $device->getConnection();

					if (!($cn instanceof \org\octris\core\db\connection_if))) {
						throw new \Exception('connection handler needs to implement interface "\org\octris\core\db\connection_if"');
					} elseif (!($cn instanceof \org\octris\core\db\pool_if)) {
						throw new \Exception('connection handler needs to implement interface "\org\octris\core\db\pool_if"');
					} else {
						$cn->setPool($type, $this);
					}
				}
			}

			return $cn;
		}

		/**
		 * Push a connection back 
		 *
		 * @octdoc  m:db/release
		 * @param 	string 								$type 			Connection type ('master' or 'slave', db::T_DB_MASTER or db::T_DB_SLAVE).
		 * @Return 	\org\octris\core\db\connection 		$cn 			Connection to release to pool.
		 */
		public function release($type, \org\octris\core\db\connection $cn)
		/**/
		{
		    array_push($this->pool[$type], $cn);
		}
	}
}
