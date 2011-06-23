<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\dbo\mongodb {
    /**
     * Handles a single document.
     *
     * @octdoc      c:mongodb/object
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    abstract class object extends \org\octris\core\type\collection
    /**/
    {
        /**
         * Object ID
         *
         * @octdoc  v:object/$_id
         * @var     MongoId
         */
        private $_id = null;
        /**/
        
        /**
         * Database connection.
         *
         * @octdoc  v:object/$pool
         * @var     \org\octris\core\dbo\mongodb\pool
         */
        protected $pool = null;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:object/__construct
         * @param   \org\octris\core\dbo\mongodb\pool       $pool           Instance of MongoDB connection pool.
         * @param   array                                   $data           Optional data to fill object with.
         */
        public function __construct(\org\octris\core\dbo\mongodb\pool $pool, array $data = array())
        /**/
        {
            $this->pool = $pool;

            if (isset($data['_id'])) {
                $this->_id = $data['_id'];
                unset($data['_id']);
            }

            parent::__construct($data);
        }

        /**
         * Makes sure, that _id is set to null, when object is cloned, because there should not exist two objects with the same object ID.
         *
         * @octdoc  m:object/__clone
         */
        public function __clone()
        /**/
        {
            $this->_id = null;
        }

        /**
         * Magic setter.
         *
         * @octdoc  m:object/__set
         * @param   string          $name                   Name of property to set.
         * @param   mixed           $value                  Value to set for property.
         */
        public function __set($name, $value)
        /**/
        {
            $this->offsetSet($name, $value);
        }
     
        /**
         * Magic getter.
         *
         * @octdoc  m:object/__get
         * @param   string          $name                   Name of property to return value of.
         * @return  mixed                                   Value of property.
         */
        public function __get($name)
        /**/
        {
            return $this->offsetGet($name);
        }
        
        /**
         * Save document object to database.
         *
         * @octdoc  m:object/save
         */
        public function save()
        /**/
        {
            $cn = $this->pool->connect(\org\octris\core\dbo::T_DBO_UPDATE);
            
            $data = parent::getArrayCopy();
            
            if (is_null($this->_id)) {
                // insert
                $this->cn->insert($data);

                if (isset($data['_id'])) {
                    $this->_id = $data['_id'];
                }
            } else {
                // update
                $this->cn->update(array('_id' => new MongoId($this->_id)), array('$set' => $data));
            }
            
            $cn->release();
        }
        
        /**
         * Prepare data for storing into MongoDB.
         *
         * @octdoc  m:object/import
         * @param   mixed       $value      Value to prepare.
         * @return  mixed                   Prepared value.
         */
        private function import($value)
        /**/
        {
            if (is_object($value))
            // handle objects
                if ($value instanceof DateTime) {
                    // convert PHP DateTime to MongoDate
                    $value = new MongoDate($value->getTimestamp());
                } elseif ($value instanceof \org\octris\core\type\collection) {
                    // convert 
                    $value = (array)$value;
                }
            } 
            
            if (is_array($value)) {
                foreach ($value as &$item) {
                    $item = $this->import($item);
                }
            }
            
            return $value;
        }
        
        /**
         * Prepare data for returning in Userland code.
         *
         * @octdoc  m:object/export
         * @param   mixed       $value      Value to prepare.
         * @return  mixed                   Prepared value.
         */
        private function export($value)
        /**/
        {
            if (is_object($value)) {
                if ($value instanceof MongoDate) {
                    // convert MongoDate to PHP DateTime
                    $value = new DateTime((string)$value);
                }
            } elseif (is_array($value)) {
                foreach ($value as &$item) {
                    $item = $this->export($item);
                }
            }
            
            return $value;
        }
        
        /** ArrayAccess **/
    
        /**
         * Returns the value at the specified index.
         *
         * @octdoc  m:object/offsetGet
         * @param   string      $offs       Offset to return the value of.
         * @return  mixed                   Value stored at specified offset.
         */
        public function offsetGet($offs)
        /**/
        {
            $return = null;                 // return 'null' for unknown object properties
            
            if ($offs == '_id') {
                // ObjectId
                $return = (string)$this->_id;
            } elseif ($this->offsetExists($offs)) {
                // known property
                $return = $this->export(parent::offsetGet($offs));
            }
            
            return $return;
        }
    
        /**
         * Set value in collection at specified offset.
         *
         * @octdoc  m:collection/offsetSet
         * @param   string      $offs       Offset to set value at.
         * @param   mixed       $value      Value to set at offset.
         */
        public function offsetSet($offs, $value)
        /**/
        {
            if ($offs == '_id') {
                // ObjectId can't be set!
                throw new Exception('unable to set protected property "' . $name . '"');
            } else {
                $value = $this->import(parent::offsetSet($offs, $value));
            }
        }
    }    
}
