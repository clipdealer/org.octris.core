<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\db\device\riak {
    /**
     * Riak database connection.
     *
     * @octdoc      c:riak/connection
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class connection implements \org\octris\core\db\device\connection_if
    /**/
    {
        /**
         * Device the connection belongs to.
         *
         * @octdoc  p:connection/$device
         * @var     \org\octris\core\db\device\riak
         */
        protected $device;
        /**/

        /**
         * URI instance.
         *
         * @octdoc  p:connection/$uri
         * @var     \org\octris\core\type\uri
         */
        protected $uri;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:connection/__construct
         * @param   \org\octris\core\db\device\riak     $device             Device the connection belongs to.
         * @param   array                               $options            Connection options.
         */
        public function __construct(\org\octris\core\db\device\riak $device, array $options)
        /**/
        {
            $this->device = $device;
            
            $this->uri = \org\octris\core\type\uri::create(
                $options['host'], $options['port']
            );
        }

        /**
         * Release connection.
         *
         * @octdoc  m:connection/release
         */
        public function release()
        /**/
        {
            $this->device->release($this);
        }

        /**
         * Check connection.
         *
         * @octdoc  m:connection/isAlive
         * @return  bool                                            Returns true if the connection is alive.
         */
        public function isAlive()
        /**/
        {
            $uri = clone($this->uri);
            $uri->path = '/ping';
            
            $result = (new \org\octris\core\net\client\http($uri))->execute();
            
            return (trim($result) == 'OK');
        }

        /**
         * Resolve a database reference.
         *
         * @octdoc  m:connection/resolve
         * @param   array           $ref                                Database reference to resolve.
         * @param   \org\octris\core\db\device\riak\dataobject       Data object.
         */
        public function resolve(array $ref)
        /**/
        {
            $return = false;

            if (!\riakRef::isRef($ref)) {
                throw new \Exception('no database reference provided');
            } else {
                $cl = $this->db->selectCollection($collection);

                $data = $cl->getDBRef($ref);

                $return = new \org\octris\core\db\device\riak\dataobject($this->device, $collection, $data);
            }

            return $return;
        }

        /**
         * Create an empty object for storing data into specified bucket.
         *
         * @octdoc  m:connection/create
         * @param   string          $bucket                             Name of bucket to create object for.
         * @return  \org\octris\core\db\device\riak\dataobject          Data object.
         */
        public function create($bucket)
        /**/
        {
            return new \org\octris\core\db\device\riak\dataobject($this->device, $bucket);
        }

        /**
         * Execute a database command.
         *
         * @octdoc  m:connection/command
         * @param   array           $command                    Command to execute in database.
         * @param   array           $options                    Optional options for command.
         * @return  mixed                                       Return value of executed command.
         */
        public function command(array $command, array $options = array())
        /**/
        {
            return $this->db->command($command, $options);
        }

        /**
         * Query the database and count the results.
         *
         * @octdoc  m:connection/count
         * @param   string          $collection                 Name of collection to query.
         * @param   array           $query                      Query conditions.
         * @param   int             $offset                     Optional offset to start query result from.
         * @param   int             $limit                      Optional limit of result items.
         * @return  int                                         Number of items found.
         */
        public function count($collection, array $query, $offset = 0, $limit = null)
        /**/
        {
            $cl = $this->db->selectCollection($collection);

            return $cl->count($query, $offset, $limit);
        }

        /**
         * Create an index in database.
         *
         * @octdoc  m:connection/ensureIndex
         * @param   string          $collection                 Name of collection to create index in.
         * @param   array           $keys                       Key(s) to create index for.
         * @param   array           $options                    Optional options for index.
         */
        public function ensureIndex($collection, array $keys, array $options = array())
        /**/
        {
            $cl = $this->db->selectCollection($collection);

            $cl->ensureIndex($keys, $options);
        }

        /**
         * Query a riak collection and return the first found item.
         *
         * @octdoc  m:connection/first
         * @param   string          $collection                         Name of collection to query.
         * @param   array           $query                              Query conditions.
         * @param   array           $sort                               Optional sorting parameters.
         * @param   array           $fields                             Optional fields to return.
         * @param   array           $hint                               Optional query hint.
         * @return  \org\octris\core\db\device\riak\dataobject|bool  Either a data object containing the found item or false if no item was found.
         */
        public function first($collection, array $query, array $sort = null, array $fields = array(), array $hint = null)
        /**/
        {
            $cursor = $this->query($collection, $query, 0, 1, $sort, $fields, $hint);

            return ($cursor->next() ? $cursor->current : false);
        }

        /**
         * Query a riak collection.
         *
         * @octdoc  m:connection/query
         * @param   string          $collection                 Name of collection to query.
         * @param   array           $query                      Query conditions.
         * @param   int             $offset                     Optional offset to start query result from.
         * @param   int             $limit                      Optional limit of result items.
         * @param   array           $sort                       Optional sorting parameters.
         * @param   array           $fields                     Optional fields to return.
         * @param   array           $hint                       Optional query hint.
         * @return  \org\octris\core\db\device\riak\result   Result object.
         */
        public function query($collection, array $query, $offset = 0, $limit = null, array $sort = null, array $fields = array(), array $hint = null)
        /**/
        {
            $cl = $this->db->selectCollection($collection);

            if (($cursor = $cl->find($query, $fields)) === false) {
                throw new \Exception('unable to query database');
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
            
            return new \org\octris\core\db\device\riak\result($this->device, $collection, $cursor);
        }

        /**
         * Insert an object into a database collection.
         *
         * @octdoc  m:connection/insert
         * @param   string          $collection                 Name of collection to insert data into.
         * @param   array           $object                     Data to insert into collection.
         */
        public function insert($collection, array $object)
        /**/
        {
            $cl = $this->db->selectCollection($collection);
        
            return $cl->insert($object);
        }

        /**
         * Update data in database collection.
         *
         * @octdoc  m:connection/update
         * @param   string          $collection                 Name of collection to update data in.
         * @param   array           $criteria                   Search criteria for object(s) to update.
         * @param   array           $object                     Data to update collection with.
         * @param   array           $options                    Optional options.
         */
        public function update($collection, array $criteria, array $object, array $options = null)
        /**/
        {
            $cl = $this->link->selectCollection($collection);
            
            return $cl->update($criteria, $object, $options);
        }

        /**
         * Remove data from database.
         *
         * @octdoc  m:connection/remove
         * @param   string          $collection                 Name of collection to remove data from.
         * @param   array           $criteria                   Search criteria for object(s) to remove.
         * @param   array           $options                    Optional options.
         */
        public function remove($collection, array $criteria, array $options = array())
        /**/
        {
            $cl = $this->link->selectCollection($collection);
            
            $cl->remove($criteria, $options);
        }
    }
}
