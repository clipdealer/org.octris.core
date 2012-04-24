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
	class mongodb extends \org\octris\core\db\device
	/**/
	{
		/**
		 * Host of database server.
		 *
		 * @octdoc  p:mongodb/$host
		 * @var     string
		 */
		protected $host;
		/**/
		
		/**
		 * Port of database server.
		 *
		 * @octdoc  p:mongodb/$port
		 * @var     int
		 */
		protected $port;
		/**/

		/**
		 * Name of database to connect to.
		 *
		 * @octdoc  p:mongodb/$database
		 * @var     string
		 */
		protected $database;
		/**/

		/**
		 * Username to use for connection.
		 *
		 * @octdoc  p:mongodb/$username
		 * @var     string
		 */
		protected $username;
		/**/
		
		/**
		 * Password to use for connection.
		 *
		 * @octdoc  p:mongodb/$password
		 * @var     string
		 */
		protected $password;
		/**/

		/**
		 * Constructor.
		 *
		 * @octdoc	m:mongodb/__construct
		 * @param 	string 			$host 				Host of database server.
		 * @param 	int 			$port 				Port of database server.
		 * @param 	string 			$database 			Name of database.
		 * @param 	string 			$username 			Username to use for connection.
		 * @param 	string 			$password 			Optional password to use for connection.
		 */
		public __construct($host, $port, $database, $username, $password = '')
		/**/
		{
			$this->host 	= $host;
			$this->port 	= $port;
			$this->database = $database;
			$this->username = $username;
			$this->password = $password;
		}

		/**
		 * Create database connection.
		 *
		 * @octdoc 	m:mongodb/getConnection
		 * @return 	\org\octris\core\db\mongodb\connection 			Connection to a MongoDB database.
		 */
		public getConnection()
		/**/
		{
			$cn = new \org\octris\core\db\mongodb\connection(
				array(
					'host'	   => $this->host,
					'port'	   => $this->port,
					'database' => $this->database,
					'username' => $this->username,
					'password' => $this->password
				)
			);

			return $cn;
		}
	}
}
