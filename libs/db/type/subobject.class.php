<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\db\type {
    /**
     * Common data object.
     *
     * @octdoc      c:type/subobject
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class subobject implements \ArrayAccess, \Countable, \IteratorAggregate
    /**/
    {
        /**
         * Data to store in object.
         *
         * @octdoc  p:subobject/$data
         * @var     array
         */
        protected $data = array();
        /**/

        /**
         * Reference to dataobject the subobject belongs to.
         * 
         * @octdoc  p:subobject/$dataobject
         * @var     \org\octris\core\db\type\dataobject
         */
        protected $dataobject;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:subobject/__construct
         * @param   array                                   $data           Data to initialize object with.
         * @param   \org\octris\core\db\type\dataobject     $dataobject     Dataobject the subobject is part of.
         */
        public function __construct(array $data = array(), \org\octris\core\db\type\dataobject $dataobject)
        /**/
        {
            $this->dataobject = $dataobject;
            
            foreach ($data as $key => $value) {
                $this[$key] = $value;
            }
        }

        /**
         * Supports deep copy cloning.
         *
         * @octdoc  m:subobject/__clone
         */
        public function __clone()
        /**/
        {
            foreach ($this->data as $key => $value) {
                if ($value instanceof self) {
                    $this[$key] = clone($value);
                }
            }
        }

        /**
         * Magic method gets called, when 'json_encode' is used on the object instance.
         *
         * @octdoc  m:subobject/jsonSerialize
         * @return  array                                   Array representation of object.
         */
        public function jsonSerialize()
        /**/
        {
            return $this->getArrayCopy();
        }

        /**
         * Convert to array.
         *
         * @octdoc  m:subobject/getArrayCopy
         * @return  array                                   Array representation of object.
         */
        public function getArrayCopy()
        /**/
        {
            $data = $this->data;

            foreach ($data as $key => $value) {
                if ($value instanceof self) {
                    $data[$key] = $value->getArrayCopy();
                }
            }

            return $data;
        }

        /**
         * Return an array of keys stored in object.
         *
         * @octdoc  m:subobject/getKeys
         * @return  array                                   Stored keys.
         */
        public function getKeys()
        /**/
        {
            return array_keys($this->data);
        }

        /**
         * Cast values to database specific types.
         *
         * @octdoc  a:subject/castTo
         */
        abstract protected function castTo($value);
        /**/

        /**
         * Cast values from database specific to PHP types.
         *
         * @octdoc  a:subject/castFrom
         */
        abstract public function castFrom($value);
        /**/

        /**
         * Create a new instance of subobject.
         *
         * @octdoc  a:subject/createSubObject
         * @param   array           $data                   Data to create subobject from.
         * @return  \org\octris\core\db\type\subobject      Created subobject.
         */
        abstract public function createSubObject(array $data);
        /**/

        /** ArrayAccess **/

        /**
         * Get object property.
         *
         * @octdoc  m:subobject/offsetGet
         * @param   string          $name                   Name of property to get.
         * @return  mixed                                   Data stored in property.
         */
        public function offsetGet($name)
        /**/
        {
            return $this->castFrom($this->data[$name]);
        }

        /**
         * Set object property.
         *
         * @octdoc  m:subobject/offsetSet
         * @param   string          $name                   Name of property to set.
         * @param   mixed           $value                  Value to set for property.
         */
        public function offsetSet($name, $value)
        /**/
        {
            if (is_array($value)) {
                $value = new self($value, $this->dataobject);
            }

            if ($name === null) {
                $this->data[] = $this->castTo($value);
            } else {
                $this->data[$name] = $this->castTo($value);
            }
        }

        /**
         * Unset an object property.
         *
         * @octdoc  m:subobject/offsetUnset
         * @param   string          $name                   Name of property to unset.
         */
        public function offsetUnset($name)
        /**/
        {
            unset($this->data[$name]);
        }

        /**
         * Test if an object property exists.
         *
         * @octdoc  m:subobject/offsetExists
         * @param   string          $name                   Name of property to test.
         * @return  bool                                    Returns true if a property exists.
         */
        public function offsetExists($name)
        /**/
        {
            return isset($this->data[$name]);
        }

        /** Countable **/

        /**
         * Returns number of items stored in object.
         *
         * @octdoc  m:subobject/count
         * @return  int                                     Number of items stored.
         */
        public function count()
        /**/
        {
            return count($this->data);
        }

        /** IteratorAggregate **/

        /**
         * Return iterator to iterate over object data.
         *
         * @octdoc  m:subobject/getIterator
         * @return  \org\octris\core\db\device\riak\dataiterator        Instance of iterator.
         */
        public function getIterator()
        /**/
        {
            return new \org\octris\core\db\type\recursivedataiterator(clone($this));
        }
    }
}
