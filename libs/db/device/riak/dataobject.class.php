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
     * Riak data object
     *
     * @octdoc      c:riak/dataobject
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class dataobject extends \org\octris\core\db\device\riak\subobject
    /**/
    {
        /**
         * Instance of mongodb device responsable for connections.
         *
         * @octdoc  p:dataobject/$device
         * @var     \org\octris\core\db\device\mongodb
         */
        protected $device;
        /**/

        /**
         * Name of collection the dataobject has access to.
         *
         * @octdoc  p:dataobject/$collection
         * @var     string
         */
        protected $collection;
        /**/

        /**
         * Headers stored with object.
         *
         * @octdoc  m:dataobject/$headers
         * @var     array
         */
        protected $headers;
        /**/

        /**
         * Content type of stored data.
         *
         * @octdoc  p:dataobject/$content_type
         * @var     string
         */
        protected $content_type = 'application/json';
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:dataobject/__construct
         * @param   \org\octris\core\db\device\riak         $device         Device the connection belongs to.
         * @param   string                                  $collection     Name of collection the dataobject belongs to.
         * @param   array                                   $data           Data to initialize dataobject with,
         */
        public function __construct(\org\octris\core\db\device\riak $device, $collection, array $data = array())
        /**/
        {
            $this->device     = $device;
            $this->collection = $collection;

            if (isset($data['_id'])) {
                $this->data['_id'] = (is_object($data['_id']) && $data['_id'] instanceof \MongoId
                                        ? $data['_id']
                                        : new \MongoId($data['_id']));

                unset($data['_id']);
            }

            parent::__construct($data);
        }

        /**
         * Make sure that object Id get's reset, when object is cloned, because no duplicate Ids
         * are allowed for objects in a bucket.
         *
         * @octdoc  m:dataobject/__clone
         */
        public function __clone()
        /**/
        {
            unset($this->data['_id']);

            parent::__clone();
        }

        /**
         * Set type of content of data stored in the object.
         *
         * @octdoc  m:dataobject/setContentType
         * @param   string                  $content_type               Content type to set.
         */
        public function setContentType($content_type)
        /**/
        {
            $this->content_type = $content_type;
        }

        /**
         * Return content type of data stored in the object.
         *
         * @octdoc  m:dataobject/getContentType
         * @return  string                                              Content type to return.
         */
        public function getContentType()
        /**/
        {
            return $this->content_type;
        }

        /**
         * Get datetime of last modification of the object. The method returns 'null' if the
         * last modified datetime is not set.
         *
         * @octdoc  m:dataobject/getLastModified
         * @return  \DateTime|null                                      Last modified datetime.
         */
        public function getLastModified()
        /**/
        {
            return (isset($this->headers['last-modified'])
                    ? new DateTime($this->headers['last-modified'])
                    : null);
        }

        /**
         * Save dataobject to bucket.
         *
         * @octdoc  m:dataobject/save
         * @return  bool                                                Returns true on success otherwise false.
         */
        public function save()
        /**/
        {
            $return = true;
            
            $cn = $this->device->getConnection(\org\octris\core\db::T_DB_MASTER);
            $cl = $cn->getCollection($this->collection);

            if (!isset($this->data['_id'])) {
                // insert new object
                $key = $cl->insert($this->data);

                if (($return = ($key !== false))) {
                    $this->data['_id'] = $key;
                }
            } else {
                // update object
                $tmp = $this->data;
                
                $key = $tmp['_id'];
                unset($tmp['_id']);

                $return = $cl->update($key, $tmp);
            }

            $cn->release();
            
            return $return;
        }

        /** ArrayAccess **/

        /**
         * Set object property.
         *
         * @octdoc  m:dataobject/offsetSet
         * @param   string          $name                   Name of property to set.
         * @param   mixed           $value                  Value to set for property.
         */
        public function offsetSet($name, $value)
        /**/
        {
            if ($name == '_id') {
                throw new \Exception('Property "_id" is read-only');
            } elseif ($name === null) {
                throw new \Exception('Property name cannot be null');
            } else {
                parent::offsetSet($name, $value);
            }
        }

        /**
         * Unset an object property.
         *
         * @octdoc  m:dataobject/offsetUnset
         * @param   string          $name                   Name of property to unset.
         */
        public function offsetUnset($name)
        /**/
        {
            if ($name == '_id') {
                throw new \Exception('property "_id" is read-only');
            } else {
                parent::offsetUnset($name);
            }
        }
    }
}
