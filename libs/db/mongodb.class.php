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
	 * MongoDB database device.
	 *
	 * @octdoc		c:db/mongodb
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
	class mongodb implements \org\octris\core\db\device_if
	/**/
	{
		/**
		 * Host of database server.
		 *
		 * @octdoc	p:mongodb/$host
		 * @var 	string
		 */
		protected $host;
		/**/

		/**
		 * Port of database server.
		 *
		 * @octdoc	p:mongodb/$port
		 * @var 	int
		 */
		protected $port;
		/**/

		/**
		 * User to use for connection.
		 *
		 * @octdoc 	p:mongodb/$user
		 * @var 	string
		 */
		protected $user;
		/**/

		/**
		 * Password to use for connection
		 *
		 * @octdoc 	p:mongodb/$password
		 * @var 	string
		 */
		protected $password;
		/**/

		/**
		 * Constructor.
		 *
		 * @octdoc	m:mongodb/__construct
		 * @param 	string 			$host 				Host of database server.
		 * @param 	int 			$port 				Port of database server.
		 * @param 	string 			$user 				User to use for connection.
		 * @param 	string 			$password 			Optional password to use for connection.
		 */
		public __construct($host, $port, $user, $password = '')
		/**/
		{
			$this->host 	= $host;
			$this->user 	= $user;
			$this->port 	= $port;
			$this->password = $password;
		}

		/**
		 * Create database connection.
		 *
		 * @octdoc 	m:mongodb/getConnection
		 * @param 	\org\octris\core\db\pool 		$pool 			Optional instance of pool, if connection is part of one.
		 * @return 	\prg\octris\core\db\mongodb\connection 			Connection to a MongoDB database.
		 */
		public getConnection(\org\octris\core\db\pool $pool = null)
		/**/
		{
			return new \org\octris\core\db\mongodb\connection($host, $port, $user, $password, $pool);
		}
	}
}
