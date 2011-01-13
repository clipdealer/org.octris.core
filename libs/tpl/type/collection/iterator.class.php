<?php

namespace org\octris\core\tpl\type\collection {
    /**
     * Implements functionality for iterating a template-type collection.
     *
     * @octdoc      c:collection/iterator
     * @copyright   copyright (c) 2010-2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class iterator implements \Iterator, \SeekableIterator, \Countable
    /**/
    {
        /**
         * Iterator position.
         *
         * @octdoc  v:iterator/$position
         * @var     int
         */
        protected $position = 0;
        /**/

        /**
         * Keys of collection data.
         *
         * @octdoc  v:iterator/$keys
         * @var     array
         */
        protected $keys = array();
        /**/

        /**
         * Collection data.
         *
         * @octdoc  v:iterator/$data
         * @var     array
         */
        protected $data = array();
        /**/

        /**
         * Number of items in collection.
         *
         * @octdoc  v:iterator/$count
         * @var     int
         */
        protected $count = 0;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:iterator/__construct
         * @param   mixed       $data       Collection data for iterating.
         */
        public function __construct($data)
        /**/
        {
            $this->position = 0;
            $this->keys     = array_keys($data);
            $this->data     = $data;
            $this->count    = count($this->keys);
        }
        
        /**
         * Return current collection item.
         *
         * @octdoc  m:iterator/current
         * @return  mixed                   Value of current collection item.
         */
        public function current()
        /**/
        {
            return $this->getItem($this->position);
        }
        
        /**
         * Return key of current collection item.
         *
         * @octdoc  m:iterator/key
         * @return  string                  Key of current collection item.
         */
        public function key()
        /**/
        {
            return $this->keys[$this->position];
        }

        /**
         * Move cursor to next collection item.
         *
         * @octdoc  m:iterator/next
         */
        public function next()
        /**/
        {
            ++$this->position;
        }
        
        /**
         * Rewind collection back to first item.
         *
         * @octdoc  m:iterator/rewind
         */
        public function rewind()
        /**/
        {
            $this->position = 0;
        }

        /**
         * Test if the current cursor position is valid. The position is valid, if the cursors points to an existing item.
         *
         * @octdoc  m:iterator/valid
         * @return  bool                    Returns true for valid positions.
         */
        public function valid()
        /**/
        {
            return ($this->position < $this->count);
        }

        /**
         * Seek to specified position in collection.
         *
         * @octdoc  m:iterator/seek
         * @param   int     $position       Position to seek to.
         */
        public function seek($position)
        /**/
        {
            $this->position = $position;
        }

        /**
         * Returns number of elements in collection.
         *
         * @octdoc  m:iterator/count
         * @return  int                     Number of elements in collection.
         */
        public function count()
        /**/
        {
            return $this->count;
        }

        /**
         * Return item for specified position.
         *
         * @octdoc  m:iterator/getItem
         * @param   int     $pos            Position to return item for.
         * @return  mixed                   Item value.
         */
        protected function getItem($pos)
        /**/
        {
            $item = $this->data[$this->keys[$pos]];
            
            switch (gettype($item)) {
            case 'object':
                $item = (array)$item;
                /** FALL THRU **/
            case 'array':
                $item = new \org\octris\core\tpl\type\collection($item);
                break;
            }
        
            return (object)array(
                'item'      => $item,
                'key'       => $this->keys[$pos],
                'pos'       => $pos,
                'count'     => $this->count,
                'is_first'  => ($pos == 0),
                'is_last'   => ($pos == $this->count - 1)
            );
        }
    }
}
