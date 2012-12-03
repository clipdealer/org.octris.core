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
    use \org\octris\core\net\client\http as http;

    /**
     * Riak database collection. Note, that a collection in Riak is called "Bucket" and so this
     * class operates on riak buckets.
     *
     * @octdoc      c:riak/collection
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class collection
    /**/
    {
        /**
         * Device the collection belongs to.
         *
         * @octdoc  p:collection/$device
         * @var     \org\octris\core\db\device\riak
         */
        protected $device;
        /**/

        /**
         * Instance of connection class the collection is access by.
         *
         * @octdoc  p:collection/$connection
         * @var     \org\octris\core\db\device\riak\connection
         */
        protected $connection;
        /**/

        /**
         * Name of collection.
         *
         * @octdoc  p:collection/$name
         * @var     string
         */
        protected $name;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:collection/__construct
         * @param   \org\octris\core\db\device\riak             $device         Device the connection belongs to.
         * @param   \org\octris\core\db\device\riak\connection  $connection     Connection instance.
         * @param   string                                      $name           Name of collection.
         */
        public function __construct(\org\octris\core\db\device\riak $device, \org\octris\core\db\device\riak\connection $connection, $name)
        /**/
        {
            $this->device     = $device;
            $this->connection = $connection;
            $this->name       = $name;
        }

        /**
         * Return name of collection.
         *
         * @octdoc  m:collection/getName
         * @return  string                                          Name of collection.
         */
        public function getName()
        /**/
        {
            return $this->name;
        }

        /**
         * Create an empty object for storing data into specified collection.
         *
         * @octdoc  m:collection/create
         * @return  \org\octris\core\db\device\riak\dataobject      Data object.
         */
        public function create()
        /**/
        {
            $object = new \org\octris\core\db\device\riak\dataobject(
                $this->device,
                $this->getName()
            );

            $object->setContentType('application/json');

            return $object;
        }

        /**
         * Insert an object into a database collection.
         *
         * @octdoc  m:collection/insert
         * @param   array           $object                     Data to insert into collection.
         * @return  string|bool                                 Returns the inserted key if insert succeeded or false.
         */
        public function insert(array $object)
        /**/
        {
            $request = $this->connection->getRequest(
                http::T_POST, 
                '/buckets/' . $this->name . '/keys'
            );
            $request->addHeader('Content-Type', 'application/json');
            $request->execute(json_encode($object));
            
            if (($return = $request->getStatus()) == 201) {
                $loc = $request->getResponseHeader('location');
                
                $return = substr($loc, strrpos($loc, '/') + 1);
            }
                
            return $return;
        }

        /**
         * Update data in database collection.
         *
         * @octdoc  m:collection/update
         * @param   string          $key                        Key to update.
         * @param   array           $object                     Data to update collection with.
         * @return  bool                                        Returns true if update succeeded otherwise false.
         */
        public function update($key, array $object)
        /**/
        {
            $request = $this->connection->getRequest(
                http::T_GET, 
                '/buckets/' . $this->name . '/keys/' . $key
            );
            $request->addHeader('Content-Type', 'application/json');
            $request->execute(json_encode($object));

            return ($request->getStatus() == 200);
        }
    }
}
