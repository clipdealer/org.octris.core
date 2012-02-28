<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\db\mongodb {
	/**
	 * MongoDB database device.
	 *
	 * @octdoc		c:mongodb/connection
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
	class connection implements \org\octris\core\db\connection_if
	/**/
	{
		use \org\octris\core\db\pool_tr;

		/**
		 * Instance of mongo class.
		 *
		 * @octdoc  p:connection/$mongo
		 * @var     \Mongo
		 */
		protected $mongo;
		/**/

		/**
		 * Connection to a database.
		 *
		 * @octdoc  p:connection/$db
		 * @var     \MongoDB
		 */
		protected $db;
		/**/

		/**
		 * Constructor.
		 *
		 * @octdoc  m:connection/__construct
		 * @param 	string 			$host 				Host of database server.
		 * @param 	int 			$port 				Port of database server.
		 * @param 	string 			$database 			Name of database.
		 * @param 	string 			$username 			Username to use for connection.
		 * @param 	string 			$password 			Optional password to use for connection.
		 */
		public function __construct($host, $port, $database, $username, $password = '')
		/**/
		{
			$this->mongo = new \Mongo(
				'mongodb://' . $host . ':' . $port,
				array(
					'username' => $username,
					'password' => $password,
					'db'	   => $database
				)
			);

			$this->db = $this->mongo->selectDB($database);
		}

		/**
		 * Create an empty object for storing data into specified collection.
		 *
		 * @octdoc  m:connection/create
		 * @param   string 			$collection 				Name of collection to create object for.
		 * @return 	\org\octris\core\db\mongodb\dataobject 		Data object.
		 */
		public function create($collection)
		/**/
		{
			$cl = $this->db->selectCollection($collection);

		    return new \org\octris\core\db\mongodb\dataobject($this->pool);
		}

		/**
		 * Query a MongoDB collection.
		 *
		 * @octdoc  m:connection/query
		 */
		public function query($collection)
		/**/
		{
		    $cl = $this->db->selectCollection($collection);
		    $cl->query();
		}
	}
}
