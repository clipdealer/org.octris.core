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
		 * Instance of MongoCollection class the object belongs to.
		 *
		 * @octdoc  p:dataobject/$cl
		 * @var     \MongoCollection
		 */
		protected $cl;
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
		 * Constructor.
		 *
		 * @octdoc  m:dataobject/__construct
		 * @param 	\MongoCollection 		$cl 			Instance of MongoCollection class.
		 */
		public function __construct(\MongoCollection $cl)
		/**/
		{
		    $this->cl = $cl;
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
		 * Save dataobject to collection.
		 *
		 * @octdoc  m:dataobject/save
		 */
		public function save()
		/**/
		{
		    if (is_null($this->_id)) {
		    	// insert new object
		    } else {
		    	// update object
		    }
		}
	}
}
