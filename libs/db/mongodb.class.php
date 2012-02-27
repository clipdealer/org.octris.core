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
		 * Instance of mongo class.
		 *
		 * @octdoc  p:mongodb/$mongo
		 * @var     \Mongo
		 */
		protected $mongo;
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
			$this->mongo = new \Mongo(
				'mongodb://' . $host . ':' . $port,
				array(
					'connect'  => false,
					'username' => $username,
					'password' => $password,
					'db'	   => $database
				)
			);

			$this->database = $database;
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
			return new \org\octris\core\db\mongodb\connection($this->mongo, $this->database);
		}
	}
}
