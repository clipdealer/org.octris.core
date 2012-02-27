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
		 * Connection.
		 *
		 * @octdoc  p:connection/$cn
		 * @var     \MongoDB
		 */
		protected $cn;
		/**/

		/**
		 * Constructor.
		 *
		 * @octdoc  m:connection/__construct
		 * @param 	\Mongo 			$mongo 				Instance of mongo class.
		 * @param 	string 			$database 			Name of database to connect to.
		 */
		public function __construct(\Mongo $mongo, $database)
		/**/
		{
			$this->mongo = $mongo;
			$this->mongo->connect();

			$this->cn = $this->mongo->selectDB($database);
		}
	}
}
