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
     * PDO connection handler.
     *
     * @octdoc      c:pdo/connection
     * @copyright   copyright (c) 2014 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class connection extends \PDO implements \org\octris\core\db\device\connection_if, \org\octris\core\db\pool_if
    /**/
    {
        // use \org\octris\core\db\pool_tr;

        /**
         * Device the connection belongs to.
         *
         * @octdoc  p:connection/$device
         * @type    \org\octris\core\db\device\pdo
         */
        protected $device;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:connection/__construct
         * @param   \org\octris\core\db\device\pdo  $device             Device the connection belongs to.
         * @param   array                           $options            Connection options.
         */
        public function __construct(\org\octris\core\db\device\pdo $device, array $options)
        /**/
        {
            parent::__construct($options['dsn'], $options['username'], $options['password'], $options['options']);
        }

        /**
         * Release a connection.
         *
         * @octdoc  m:connection/release
         */
        public function release()
        /**/
        {
            parent::release();
        }

        /**
         * Query the database.
         *
         * @octdoc  m:connection/query
         * @param   string              $statement            SQL statement to perform.
         * @param   mixed               ...$params            Optional additional options.
         * @return  \org\octris\core\db\pdo\result            Query result.
         */
        public function query($statement, ...$params)
        /**/
        {
            if (($res = parent::query($statement, ...$params)) === false) {
                throw new \Exception($this->errorInfo()[2], $this->errorCode());
            }

            return new \org\octris\core\db\pdo\result($res);
        }

        /**
         * Initialize prepared statement.
         *
         * @octdoc  m:connection/prepare
         * @param   string              $statement            SQL statement to prepare.
         * @param   array               $options              Optional additional driver options.
         * @return  \org\octris\core\db\pdo\statement         Instance of a prepared statement.
         */
        public function prepare($statement, array $options = array())
        /**/
        {
            if (($stmt = parent::prepare($statement, $options)) === false) {
                throw new \Exception('PDO prepare');
            }

            return new \org\octris\core\db\pdo\statement($stmt)
        }
    }
}
