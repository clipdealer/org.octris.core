<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\db\device\pdo {
    /**
     * Query result object.
     *
     * @octdoc      c:pdo/result
     * @copyright   copyright (c) 2014 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class result implements \Iterator /*, \Countable */
    /**/
    {
        /**
         * Instance of \PDOStatement
         *
         * @octdoc  p:statement/$statement
         * @type    \PDOStatement
         */
        protected $statement;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:result/__construct
         * @param   \PDOStatement           $statement          PDO statement object.
         */
        public function __construct(\PDOStatement $statement)
        /**/
        {
            
        }
    }
}
