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
	 * Query result object.
	 *
	 * @octdoc		c:mongodb/result
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
	class result
	/**/
	{
		/**
		 * Instance of connection pool.
		 *
		 * @octdoc  p:result/$pool
		 * @var     \org\octris\core\db
		 */
		protected $pool;
		/**/

		/**
		 * Name of collection the result belongs to.
		 *
		 * @octdoc  p:result/$collection
		 * @var     string
		 */
		protected $collection;
		/**/

		/**
		 * MongoDB result cursor.
		 *
		 * @octdoc  p:result/$cursor
		 * @var     \MongoCursor
		 */
		protected $cursor;
		/**/
		
		/**
		 * Constructor.
		 *
		 * @octdoc  m:result/__construct
		 * @param 	\org\octris\core\db 		$pool 			Instance of pool responsable for connections.
		 * @param 	string 						$collection 	Name of collection the result belongs to.
		 * @param 	\MongoCursor 				$cursor 		Cursor of query result.
		 */
		public function __construct(\org\octris\core\db $pool, $collection, \MongoCursor $cursor)
		/**/
		{
		    $this->pool   	  = $pool;
		    $this->collection = $collection;
		    $this->cursor 	  = $cursor;
		}

		/**
		 * Fetch next result object.
		 *
		 * @octdoc  m:result/fetchNext
		 * @return 	\org\octris\core\db\mongodb\dataobject 		Dataobject of result item.
		 */
		public function fetchNext()
		/**/
		{
			$dataobject = false;

			if ($this->cursor->hasNext()) {
				$dataobject = new \org\octris\core\db\mongodb\dataobject(
					$this->pool, 
					$this->collection,
					$this->cursor->getNext()
				);
			}
        
	        return $dataobject;
    	}
    }
}
