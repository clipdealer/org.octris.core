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
     * Iterator for recursive iterating data objects of query results
     *
     * @octdoc      c:db/dataobject
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class recursivedataiterator implements \RecursiveIterator
    /**/
    {
        /**
         * The dataobject to iterate.
         *
         * @octdoc  p:recursivedataiterator/$data
         * @var     \org\octris\core\db\type\subobject
         */
        protected $data;
        /**/

        /**
         * Keys stored in dataobject.
         *
         * @octdoc  p:recursivedataiterator/$keys
         * @var     array
         */
        protected $keys;
        /**/

        /**
         * Internal pointer position.
         *
         * @octdoc  p:recursivedataiterator/$position
         * @var     int
         */
        protected $position = 0;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:recursivedataiterator/__construct
         * @parem   \org\octris\core\db\type\subobject    $dataobject         The dataobject to iterate.
         */
        public function __construct(\org\octris\core\db\type\subobject $dataobject)
        /**/
        {
            $this->data = $dataobject;          
            $this->keys = $dataobject->getKeys();
        }

        /** Iterator **/

        /**
         * Returns an iterator for the current item.
         *
         * @octdoc  m:recursivedataiterator/getChildren
         * @return  \org\octris\core\db\type\recursivedataiterator          Recursive data iterator for item.
         */
        public function getChildren()
        /**/
        {
            return new static($this->data[$this->keys[$this->position]]);
        }
        
        /**
         * Returns if an iterator can be created fot the current item.
         *
         * @octdoc  m:recursivedataiterator/hasChildren
         * @return  bool                                                    Returns true if an iterator can be
         *                                                                  created for the current item.
         */
        public function hasChildren()
        /**/
        {
            $item = $this->data[$this->keys[$this->position]];
            
            return (is_object($item) && $item instanceof \org\octris\core\db\type\subobject);
        }

        /**
         * Get value of item.
         *
         * @octdoc  m:recursivedataiterator/current
         * @return  mixed                                                               Value stored at current position.
         */
        public function current()
        /**/
        {
            return $this->data[$this->keys[$this->position]];
        }

        /**
         * Get key of current item.
         *
         * @octdoc  m:recursivedataiterator/key
         * @return  scalar                                                              Key of current position.
         */
        public function key()
        /**/
        {
            return $this->keys[$this->position];
        }

        /**
         * Advance pointer.
         *
         * @octdoc  m:recursivedataiterator/next
         */
        public function next()
        /**/
        {
            ++$this->position;
        }

        /**
         * Reset pointer.
         *
         * @octdoc  m:recursivedataiterator/rewind
         */
        public function rewind()
        /**/
        {
            $this->position = 0;
        }

        /**
         * Test if current pointer position is valid.
         *
         * @octdoc  m:recursivedataiterator/valid
         * @return  bool                                                                True, if position is valid.
         */
        public function valid()
        /**/
        {
            return isset($this->keys[$this->position]);
        }
    }
}
