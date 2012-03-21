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
	class connection implements \org\octris\core\db\connection_if, \org\octris\core\db\pool_if
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
         * @param   array                       $options            Connection options.
		 */
		public function __construct(array $options)
		/**/
		{
			$this->mongo = new \Mongo(
				'mongodb://' . $options['host'] . ':' . $options['port'],
				array(
					'username' => $options['username'],
					'password' => $options['password'],
					'db'	   => $options['database']
				)
			);

			$this->db = $this->mongo->selectDB($database);
		}

		/**
		 * Resolve a database reference.
		 *
		 * @octdoc  m:connection/resolve
		 * @param 	array 			$ref 						Database reference to resolve.
		 * @param 	\org\octris\core\db\mongodb\dataobject 		Data object.
		 */
		public function resolve(array $ref)
		/**/
		{
			$return = false;

	    	if (!\MongoDBRef::isRef($ref)) {
	    		throw new \Exception('no database reference provided');
	    	} else {
	    		$cl = $this->db->selectCollection($collection);

	    		$data = $cl->getDBRef($ref);

	    		$return = new \org\octris\core\db\mongodb\dataobject($this->getPool(), $collection, $data);
	    	}

	    	return $return;
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
		    return new \org\octris\core\db\mongodb\dataobject($this->getPool(), $collection);
		}

		/**
		 * Query a MongoDB collection.
		 *
		 * @octdoc  m:connection/query
	     * @param 	string 			$collection 				Name of collection to query.
	     * @param 	array 			$query 						Query conditions.
	     * @param 	int 			$offset 					Optional offset to start query result from.
	     * @param 	int 			$limit 						Optional limit of result items.
	     * @param 	array 			$sort 						Optional sorting parameters.
	     * @param 	array 			$fields 					Optional fields to return.
	     * @param 	array 			$hint 						Optional query hint.
		 */
		public function query($collection, array $query, $offset = 0, $limit = null, array $sort = null, array $fields = array(), array $hint = null)
		/**/
		{
		    $cl = $this->db->selectCollection($collection);

	        if (($cursor = $cl->find($query, $fields)) === false) {
	            throw new \Exception('unable to query database');
	        } else {
	            if (!is_null($sort)) {
	                $cursor->sort($sort);
	            }
	            if ($offset > 0) {
	                $cursor->skip($offset);
	            }
	            if (!is_null($limit)) {
	                $cursor->limit($limit);
	            }
	        }
	        
	        return new \org\octris\core\db\mongodb\result($this->getPool(), $collection, $cursor);
		}

		/**
		 * Insert an object into a database collection.
		 *
		 * @octdoc  m:connection/insert
		 * @param 	string 			$collection 				Name of collection to insert data into.
		 * @param 	array 			$object 					Data to insert into collection.
		 */
		public function insert($collection, array $object)
		/**/
		{
        	$cl = $this->db->selectCollection($collection);
        
        	return $cl->insert($object);
        }

        /**
         * Update data in database collection.
         *
         * @octdoc  m:connection/update
         * @param 	string 			$collection 				Name of collection to update data in.
         * @param 	array 			$criteria 					Search criteria for object(s) to update.
         * @param 	array 			$object 					Data to update collection with.
         * @param 	array 			$options 					Optional options.
         */
        public function update($collection, array $criteria, array $object, array $options = null)
        /**/
        {
	        $cl = $this->link->selectCollection($collection);
	        
	        return $cl->update($criteria, $object, $options);
	    }
	}
}
