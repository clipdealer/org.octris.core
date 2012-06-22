<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\db\device\sqlite {
	/**
	 * SQLite prepared statement.
	 *
	 * @octdoc		c:sqlite/statement
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
	class statement
	/**/
	{
		/**
		 * Instance of SQLite3Stmt class.
		 *
		 * @octdoc  p:statement/$instance
		 * @var     \SQLite3Stmt
		 */
		protected $instance;
		/**/

		/**
		 * Parameter types.
		 *
		 * @octdoc  p:statement/$types
		 * @var     array
		 */
		protected static $types = array(
			'i' => SQLITE3_INTEGER,
			'd' => SQLITE3_FLOAT,
			's' => SQLITE3_TEXT,
			'b' => SQLITE3_BLOB
		);
		/**/
		
		/**
		 * Constructor.
		 *
		 * @octdoc  m:statement/__construct
		 * @param 	\SQLite3 		$link 				Database connection.
		 * @param 	string 			$sql 				SQL statement.
		 */
		public function __construct(\SQLite3 $link, $sql)
		/**/
		{
			$this->instance = new \SQLite3Stmt($link, $sql);
		}

		/**
		 * Returns number of parameters in statement.
		 *
		 * @octdoc  m:statement/paramCount
		 * @return 	int 								Number of parameters.
		 */
		public function paramCount()
		/**/
		{
		    return $this->instance->paramCount();
		}

		/**
		 * Bind parameters to statement.
		 *
		 * @octdoc  m:statement/bindParam
		 * @param 	string 			$types 				String of type identifiers.
		 * @param 	array 			$values 			Array of values to bind.
		 */
		public function bindParam($types, array $values)
		/**/
		{
			if (preg_match('/[^idsb]/', $types)) {
				throw new \Exception('unknown data type in "' . $types . '"');
			} elseif (strlen($types) != ($cnt1 = count($values))) {
				throw new \Exception('number of specified types and values does not match');
			} elseif ($cnt1 != ($cnt2 = $this->paramCount())) {
				throw new \Exception(
					sprintf(
						'number of specified parameters (%d) does not match required parameters (%d)',
						$cnt1,
						$cnt2
					)
				);
			} else {
				for ($i = 0, $len = strlen($types); $i < $len; ++$i) {
					$this->instance->bindParam(
						$i + 1,
						$values[$i],
						(is_null($values[$i]) ? SQLITE3_NULL : self::$types[$types[$i]])
					);
				}
			}
		}
	}
}
