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
     * @octdoc      c:mongodb/subobject
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
         * Constructor.
         *
         * @octdoc  m:subobject/__construct
         * @param   array           $data           Data to initialize object with,
         */
        public function __construct(array $data = array())
        /**/
        {
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
         * Cast values to MongoDB specific types.
         *
         * @octdoc  m:subject/cast
         */
        protected function cast($value)
        /**/
        {
            return $value;
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
                $value = new self($value);
            }

            if ($name === null) {
                $this->data[] = $this->cast($value);
            } else {
                $this->data[$name] = $this->cast($value);
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
    }
}
