<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\db {
    /**
     * Pooling for database connections.
     *
     * @octdoc      c:pool/pool
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class pool
    /**/
    {
        /**
         * Name of database device the pool handles.
         *
         * @octdoc  p:pool/$device_name
         * @var     string
         */
        protected $device_name;
        /**/

        /**
         * Stores database master device.
         * 
         * @octdoc  p:pool/$master
         * @var     \org\octris\core\db\device_if
         */
        protected $master;
        /**/

        /**
         * Stores database slave devices.
         *
         * @octdoc  p:pool/$slaves
         * @var     array
         */
        protected $slaves = array();
        /**/

        /**
         * Storage of free database connections.
         *
         * @octdoc  p:pool/$pool
         * @var     array
         */
        protected $pool = array(
            'master' => array(),
            'slave'  => array()
        );
        /**/

        /**
         * Constructor creates a new pool and initializes it with a database device, which will be used as master
         * connection to a database.
         *
         * @octdoc  m:pool/__construct
         * @param   \org\octris\core\db\device      $master             Database device to set as master connection.
         */
        public function __construct(\org\octris\core\db\device $master)
        /**/
        {
            $this->slaves[] = $this->master = $master;
            $this->device_name = get_class($master);
        }

        /**
         * Add a database device for a slave connection to the datbase.
         *
         * @octdoc  m:pool/addSlave
         * @param   \org\octris\core\db\device      $slave              Database device to set as slave connection.
         */
        public function addSlave(\org\octris\core\db\device $slave)
        /**/
        {
            if (!($slave instanceof $this->device_name)) {
                throw new \Exception('master and slaves must be of the same device');
            } else {
                $this->slaves[] = $slave;
            }
        }

        /**
         * Return a connection of specified type.
         *
         * @octdoc  m:pool/getConnection
         * @param   string          $type               Connection type ('master' or 'slave', db::T_DB_MASTER or db::T_DB_SLAVE).
         * @Return  \org\octris\core\db\connection      Connection.
         */
        public function getConnection($type)
        /**/
        {
            if ($type != \org\octris\core\db::T_DB_MASTER && $type != \org\octris\core\db::T_DB_SLAVE) {
                throw new \Exception('unknown connection type "' . $type . '"');
            } else {
                if (!($cn = array_shift($this->pool[$type]))) {
                    // no more connections in the pool, create new one
                    if ($type == \org\octris\core\db::T_DB_MASTER) {
                        // there's only one master host
                        $device = $this->master;
                    } else {
                        // pick a random slave host
                        shuffle($this->slaves);

                        $device = $this->slaves[0];
                    }

                    $cn = $device->getConnection();

                    if (!($cn instanceof \org\octris\core\db\connection_if))) {
                        throw new \Exception('connection handler needs to implement interface "\org\octris\core\db\connection_if"');
                    } elseif (!($cn instanceof \org\octris\core\db\pool_if)) {
                        throw new \Exception('connection handler needs to implement interface "\org\octris\core\db\pool_if"');
                    } else {
                        $cn->setPool($type, $this);
                    }
                }
            }

            return $cn;
        }

        /**
         * Push a connection back 
         *
         * @octdoc  m:pool/release
         * @param   string                              $type           Connection type ('master' or 'slave', db::T_DB_MASTER or db::T_DB_SLAVE).
         * @Return  \org\octris\core\db\connection      $cn             Connection to release to pool.
         */
        public function release($type, \org\octris\core\db\connection $cn)
        /**/
        {
            array_push($this->pool[$type], $cn);
        }
    }
}
