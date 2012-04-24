<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\db\device\mongodb {
	/**
	 * Query result object.
	 *
	 * @octdoc		c:mongodb/result
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
	class result implements \Iterator, \Countable
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
		 * Count number of items in the result set.
		 *
		 * @octdoc  m:result/count
		 * @return 	int 										Number of items in the result-set.
		 */
		public function count()
		/**/
		{
		    return $this->cursor->count();
		}

		/**
		 * Return current item of the search result.
		 *
		 * @octdoc  m:result/current
		 * @return 	\org\octris\core\db\device\mongodb\dataobject|bool 	Returns either a dataobject with the stored contents of the current item or false, if the cursor position is invalid.
		 */
		public function current()
		/**/
		{
			if (!$this->valid()) {
				$return = false;
			} else {
				$return = new \org\octris\core\db\device\mongodb\dataobject(
					$this->pool, 
					$this->collection,
					$this->cursor->current()
				);
			}

			return $return;
		}

		/**
		 * Advance cursor to the next item.
		 *
		 * @octdoc  m:result/next
		 */
		public function next()
		/**/
		{
			$this->cursor->next();
		}

		/**
		 * Returns the object-ID of the current search result item.
		 *
		 * @octdoc  m:result/key
		 * @return 	string 										Object-ID.
		 */
		public function key()
		/**/
		{
		    return $this->cursor->key();
		}

		/**
		 * Rewind cursor.
		 *
		 * @octdoc  m:result/rewind
		 */
		public function rewind()
		/**/
		{
		    $this->cursor->rewind();
		}

		/**
		 * Tests if cursor position is valid.
		 *
		 * @octdoc  m:result/valid
		 * @return 	bool 										Returns true, if cursor position is valid.
		 */
		public function valid()
		/**/
		{
		    return $this->cursor->valid();
		}
    }
}
