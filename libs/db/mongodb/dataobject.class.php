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
	 * MongoDB data object
	 *
	 * @octdoc		c:mongodb/dataobject
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
	class dataobject
	/**/
	{
		/**
		 * Instance of pool responsable for connection
		 *
		 * @octdoc  p:dataobject/$pool
		 * @var     \org\octris\core\db
		 */
		protected $pool;
		/**/

		/**
		 * Object Id.
		 *
		 * @octdoc  p:dataobject/$_id
		 * @var     string|null
		 */
		protected $_id = null;
		/**/

		/**
		 * Name of collection the dataobject has access to.
		 *
		 * @octdoc  p:dataobject/$collection
		 * @var     string
		 */
		protected $collection;
		/**/

		/**
		 * Data to store in object.
		 *
		 * @octdoc  p:dataobject/$data
		 * @var     array
		 */
		protected $data = array();
		/**/

		/**
		 * Constructor.
		 *
		 * @octdoc  m:dataobject/__construct
		 * @param 	\org\octris\core\db 		$pool 		Instance of pool responsable for connections.
		 * @param 	
		 * @param 	string
		 */
		public function __construct(\org\octris\core\db $pool, $collection)
		/**/
		{
		    $this->pool 	  = $pool;
		    $this->collection = $collection;
		}

		/**
		 * Make sure that object Id get's reset, when object is cloned, because no duplicate Ids 
		 * are allowed for objects in a collection.
		 *
		 * @octdoc  m:dataobject/__clone
		 */
		public function __clone()
		/**/
		{
		    $this->_id = null;
		}

		/**
		 * Magic getter for object properties.
		 *
		 * @octdoc  m:dataobject/__get
		 * @param 	string 		$name 						Name of property to get.
		 */
		public function __get($name)
		/**/
		{
		    
		}

		/**
		 * Magic setter for object properties.
		 *
		 * @octdoc  m:dataobject/__set
		 * @param 	string 		$name 						Name of property to set.
		 * @param 	mixed 		$value 						Value to set for property.
		 */
		public function __set($name, $value)
		/**/
		{
		    if ($name == '_id') {
		    	throw new \Exception('property "_id" is read-only');
		    } else {
		    	
		    }
		}

		/**
		 * Load object with specified Id.
		 *
		 * @octdoc  m:dataobject/load
		 */
		public function load($_id)
		/**/
		{
		    $cn = $this->pool->getConnection(\org\octris\core\db::T_DB_SLAVE);

		    $cn->query($this->collection);

		    $cn->release();
		}

		/**
		 * Save dataobject to collection.
		 *
		 * @octdoc  m:dataobject/save
		 */
		public function save()
		/**/
		{
			$cn = $this->pool->getConnection(\org\octris\core\db::T_DB_MASTER);

		    if (is_null($this->_id)) {
		    	// insert new object
		    } else {
		    	// update object
		    }

		    $cn->release();
		}
	}
}
