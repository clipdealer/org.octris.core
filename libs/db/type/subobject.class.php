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
         * @type    array
         */
        protected $data = array();
        /**/

        /**
         * Reference to dataobject the subobject belongs to.
         * 
         * @octdoc  p:subobject/$dataobject
         * @type    \org\octris\core\db\type\dataobject
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
         * Merge specified data into dataobject. Note, that the method will throw an exception, if the data to
         * merge contains a new object ID.
         *
         * @octdoc  m:subobject/merge
         * @param   array                                   $data           Data to merge.
         */
        public function merge(array $data)
        /**/
        {
            foreach ($data as $key => $value) {
                $this[$key] = $value;
            }
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
            return $this->data[$name];
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
                $this->data[] = $value;
            } else {
                $this->data[$name] = $value;
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
