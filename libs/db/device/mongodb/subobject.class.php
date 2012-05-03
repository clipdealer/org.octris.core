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
         * Cast values to MongoDB specific types.
         *
         * @octdoc  m:subject/castTo
         */
        protected function castTo($value)
        /**/
        {
            if (is_object($value) && !($value instanceof self)) {
                if ($value instanceof \org\octris\core\type\number) {
                    $return = ($value->isDecimal()
                                ? (float)(string)$value
                                : new \MongoInt64((string)$value));
                } elseif ($value instanceof \org\octris\core\type\money) {
                    $return = (float)(string)$value;
                } elseif ($value instanceof \DateTime) {
                    $tmp = explode('.', $value->format('U.u'));

                    $return = \MongoDate($tmp[0], $tmp[1]);
                } else {
                    $return = (string)$value;
                }
            } else {
                $return = $value;
            }

            return $return;
        }

        /**
         * Cast values from MongoDB to PHP types.
         *
         * @octdoc  m:subject/castFrom
         */
        public function castFrom($value)
        /**/
        {
            if (is_object($value) && !($value instanceof self)) {
                if ($value instanceof \MongoDate) {
                    $return = new \org\octris\core\type\datetime((float)($value->sec . '.' . $value->usec));
                } elseif ($value instanceof \MongoId) {
                    $return = (string)$value;
                } elseif ($value instanceof \MongoInt32) {
                    $return = new \org\octris\core\type\number((string)$value);
                } elseif ($value instanceof \MongoInt64) {
                    $return = new \org\octris\core\type\number((string)$value);
                } else {
                    $return = (string)$value;
                }
            } else {
                $return = $value;
            }

            return $return;
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
            if (is_array($value) && !\MongoDBRef::isRef($value)) {
                $value = new self($value);
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
         * @return  \org\octris\core\db\device\mongodb\dataiterator     Instance of iterator.
         */
        public function getIterator()
        /**/
        {
            return new \org\octris\core\db\device\mongodb\dataiterator(clone($this));
        }
    }
}
