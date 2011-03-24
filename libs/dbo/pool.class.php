<?php

namespace org\octris\core\dbo {
    /**
     * PHP Implementation of a connection pool. Handles database connections.
     *
     * @octdoc      c:dbo/pool
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    abstract class pool
    /**/
    {
        /**
         * Connection parameters of pool instance.
         *
         * @octdoc  v:pool/$params
         * @var     array
         */
        protected $params = array();
        /**/
        
        /**
         * Connection pool handled by pool instance.
         *
         * @octdoc  v:pool/$pool
         * @var     array
         */
        protected $pool = array(
            'master' => array(),
            'slaves' => array()
        );
        /**/
        
        /**
         * Constructor creates new connection pool for specified database connection parameters.
         *
         * @octdoc  m:pool/__construct
         * @param   array           $params                 Database connection parameters.
         */
        public function __construct(array $params)
        /**/
        {
            if (!is_array($params['master'])) {
                $params['master'] = array($params['master']);
            }

            if (!isset($params['slaves']) || !is_array($params['slaves']) || count($params['slaves']) == 0) {
                $params['slaves'] = $params['master'];
            }

            $this->params = $params;
        }

        /**
         * Open a new connection to the database. This method needs to be implemented by database specific pool implementation.
         *
         * @octdoc  m:pool/getConnection
         * @param   string          $type                   Connection type to return a connection for.
         * @param   array           $params                 Connection parameters for database connection.
         * @return  object                                  Instance of database connection.
         */
        abstract protected function getConnection($type, array $params);
        /**/

        /**
         * Return a free connection of the specified type from the pool.
         *
         * @octdoc  m:pool/connect
         * @param   string          $type                   Connection type to return a connection for.
         * @return  object                                  Instance of database connection.
         */
        final public function connect($type)
        /**/
        {
            if ($type != \org\octris\core\dbo::T_CN_MASTER && $type != \org\octris\core\dbo::T_CN_SLAVE) {
                throw new \Exception("unknown connection type '$type'");
            }
            
            // TODO: shuffle before shifting?
            if (count($this->pool[$type]) == 0) {
                // create new database connection, if no connection of specified type is available.
                shuffle($this->params[$type]);
                
                $params        = $this->params;
                $params[$type] = $this->params[$type][0];
                
                if (($cn = $this->getConnection($type, $params))) {
                    $this->pool[$type][] = $cn;
                } else {
                    throw new \Exception('unable to connection to database');
                }
            } else {
                // first free connection from pool
                $cn = array_shift($this->pool[$type]);
            }
            
            return $cn;
        }
        
        /**
         * Release a connection to the pool.
         *
         * @octdoc  m:pool/release
         * @param   string          $type                   Type of connection.
         * @param   object          $cn                     Instance of database connection to release to the pool.
         */
        final public function release($type, $cn)
        /**/
        {
            array_push($this->pool[$type], $cn);
        }
    }
}
