<?php

namespace org\octris\core\dbo\mongodb {
    /**
     * Handles a single document.
     *
     * @octdoc      c:mongodb/object
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    abstract class object
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
         * @octdoc  v:object/$cn
         * @var     
         */
        private $cn = null;
        /**/

        /**
         * Document data.
         *
         * @octdoc  v:object/$data
         * @var     array
         */
        protected $data = array();
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:object/__construct
         * @param   \org\octris\core\dbo\mongodb\connection     $cn             Instance of mongodb database connection.
         * @param   array                                       $data           Optional data to fill object with.
         */
        public function __construct(\org\octris\core\dbo\mongodb\connection $cn, array $data = array())
        /**/
        {
            $this->cn = $cn;

            if (isset($data['_id'])) {
                $this->_id = $data['_id'];
                unset($data['_id']);
            }

            $this->data = $data;
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
            if ($name == '_id') {
                throw new Exception('unable to set protected property "' . $name . '"');
            } elseif (is_object($value))
                if ($value instanceof DateTime) {
                    $this->data[$name] = new MongoDate($value->getTimestamp());
                } else {
                    $this->data[$name] = $value;
                }
            } else {
                $this->data[$name] = $value;
            }
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
            $return = null;
            
            // handle ObjectId and unknown properties
            if ($name == '_id') {
                $return = $this->_id;
            } elseif (!array_key_exists($name, $this->data)) {
                $return = null;
            } elseif (is_object($this->data[$name])) {
                if ($this->data[$name] instanceof MongoDate) {
                    $return = new DateTime((string)$this->data[$name]);
                } else {
                    $return = (string)$this->data[$name];
                }
            }
            
            return $return;
        }
        
        /**
         * Save document object to database.
         *
         * @octdoc  m:object/save
         */
        public function save()
        /**/
        {
            if (is_null($this->_id)) {
                // insert
                $data = $this->data;

                $this->cn->insert($data);

                if (isset($data['_id'])) {
                    $this->_id = $data['_id'];
                }
            } else {
                // update
                $this->cn->update(array('_id' => new MongoId($this->_id)), array('$set' => $this->data));
            }
        }
    }    
}
