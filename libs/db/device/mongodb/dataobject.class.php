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
     * MongoDB data object
     *
     * @octdoc      c:mongodb/dataobject
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class dataobject extends \org\octris\core\db\device\mongodb\subobject
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
         * Constructor.
         *
         * @octdoc  m:dataobject/__construct
         * @param   \org\octris\core\db\device\mongodb      $device         Device the connection belongs to.
         * @param   string                                  $collection     Name of collection to dataobject belongs to.
         * @param   array                                   $data           Data to initialize dataobject with,
         */
        public function __construct(\org\octris\core\db\device\mongodb $device, $collection, array $data = array())
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
         * are allowed for objects in a collection.
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
         * Save dataobject to collection.
         *
         * @octdoc  m:dataobject/save
         */
        public function save()
        /**/
        {
            $cn = $this->device->getConnection(\org\octris\core\db::T_DB_MASTER);

            $tmp = $this->data;         // workaround strance reference issue with pecl_mongo

            if (!isset($tmp['_id'])) {
                // insert new object
                $cn->insert($tmp);

                if (isset($tmp['_id'])) {
                    $this->data['_id'] = $tmp['_id'];
                }
            } else {
                // update object
                $_id = $tmp['_id'];
                unset($tmp['_id']);

                $cn->update(
                    array('_id'  => $_id),
                    array('$set' => $tmp)
                );
            }

            $cn->release();
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
