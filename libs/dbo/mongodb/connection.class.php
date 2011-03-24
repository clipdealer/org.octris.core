<?php

namespace org\octris\core\dbo\mongodb {
    /**
     * Handles connection to mongodb database.
     *
     * @octdoc      c:mongodb/connection
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class connection
    /**/
    {
        /**
         * Stores instance of pool that should handle this connection.
         *
         * @octdoc  v:connection/$pool
         * @var     \org\octris\core\dbo\mongodb\pool
         */
        protected $pool = null;
        /**/
        
        /**
         * Connection type of this instance.
         *
         * @octdoc  v:connection/$type
         * @var     string
         */
        protected $type = '';
        /**/
        
        /**
         * Connection to database.
         *
         * @octdoc  v:connection/$cn
         * @var     MongoDB
         */
        protected $cn = null;
        /**/
        
        /**
         * Constructor.
         *
         * @octdoc  m:connection/__construct
         * @param   string                              $type                   Type of connection.
         * @param   \org\octris\core\dbo\mongodb\pool   $pool                   Instance of connection pool or 'null' if no pool is used.
         * @param   string                              $server                 Server host to connect to.
         * @param   string                              $username               Username to connect with.
         * @param   string                              $password               Password to connect with.
         * @param   string                              $port                   Port number to use for connection.
         */
        public function __construct($type, \org\octris\core\dbo\mongodb\pool $pool, $server, $username, $password, $database, $port)
        /**/
        {
            $this->type = $type;
            $this->pool = $pool;

            $db = new \mongo($server, true);
            $this->cn = $db->selectDB($database);
        }
        
        /**
         * Insert object into database collection.
         *
         * @octdoc  m:connection/insert
         * @param   string              $collection             Name of collection to insert object into.
         * @param   array               $object                 Object data to insert.
         * @return  bool                                        
         */
        public function insert($collection, array $object)
        /**/
        {
            $c = $this->cn->selectCollection($collection);

            return $c->insert($object);
        }

        /**
         * Update object in database collection.
         *
         * @octdoc  m:connection/update
         * @param   string              $collection             Name of collection to update.
         * @param   array               $criteria               Search criteria for object(s) to update.
         * @param   array               $object                 New object to replace old object with.
         * @param   array               $options                Optional additional options.
         */
        public function update($collection, array $criteria, array $object, array $options = null)
        /**/
        {
            $c = $this->cn->selectCollection($collection);

            return $c->update($criteria, $object, $options);
        }

        /**
         * Remove object(s) from database.
         *
         * @octdoc  m:connection/remove
         * @param   string              $collection             Name of collection to remove object(s) from.
         * @param   array               $criteria               Criteria to find object(s) that should be removed.
         * @param   bool                $one                    Optional flag if set to true, only to first occurence wil be removed.
         */
        public function remove($collection, array $criteria, $one = false)
        /**/
        {
            $c = $this->cn->selectCollection($collection);

            return $c->remove($criteria, !!$one);
        }

        /**
         * Query the database.
         *
         * @octdoc  m:connection/query
         * @param   string              $collection             Name of collection to query.
         * @param   array               $query                  Optional query parameters.
         * @param   int                 $offset                 Optional offset to start query at.
         * @param   int                 $limit                  Optionally limit result with this parameter.
         * @param   array               $sort                   Optional sorting parameters.
         * @param   array               $fields                 Optionally limit returned fields.
         * @param   array               $hint                   Optional query hint.
         * @param   \org\octris\core\dbo\mongodb\result         Result object.
         */
        public function query($collection, array $query = array(), $offset = 0, $limit = null, array $sort = null, array $fields = array(), array $hint = null) 
        /**/
        {
            $c = $this->cn->selectCollection($collection);

            if (($cursor = $c->find($query, $fields)) === false) {
                throw new Exception('unable to query database!');
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

            return new \org\octris\core\dbo\mongodb\result($cursor);
        }

        /**
         * Count the selected collection.
         *
         * @octdoc  m:connection/count
         * @param   string              $collection             Name of collection to query and count.
         * @param   array               $criteria               Criteria to filter collection with.
         * @return  int                                         Number of items found.
         */
        public function count($collection, array $criteria = null)
        /**/
        {
            $c = $this->cn->selectCollection($collection);

            if (is_null($criteria)) {
                return $c->count();
            } else {
                return $c->count($criteria);
            }
        }

        /**
         * Execute server-side javascript code.
         *
         * @octdoc  m:connection/execute
         * @param   string              $code                   Javascript code to execute.
         * @param   array               $args                   Optional arguments for Javascript code.
         * @return  array                                       Returned data.
         */
        public function execute($code, array $args = array())
        /**/
        {
            return $this->cn->execute($code, $args);
        }

        /**
         * Issue a command on the database server.
         *
         * @octdoc  m:connection/command
         * @param   array               $cmd                    Command and options to execute on the server.
         * @return  array                                       Returned data.
         */
        public function command(array $cmd)
        /**/
        {
            return $this->cn->command($cmd);
        }

        /**
         * Resolve a database reference.
         *
         * @octdoc  m:connection/getReference
         * @param   array               $ref                    Database reference.
         * @return  array                                       Resolved reference.
         */
        public function getReference(array $ref)
        /**/
        {
            return $this->cn->getDBRef($ref);
        }

        /**
         * Returns last occured error.
         *
         * @octdoc  m:connection/getLastError
         * @return  string                                      Error message.
         */
        public function getLastError()
        /**/
        {
            return $this->cn->lastError();
        }

        /**
         * Release connection and make it available to the pool.
         *
         * @octdoc  m:connection/release
         */
        public function release()
        /**/
        {
            if (is_null($this->pool)) {
                // TODO: close mongodb connection
            } else {
                $this->pool->release($this->type, $this);
            }
        }
    }
}
