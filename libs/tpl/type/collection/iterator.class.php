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
        function __construct($data)
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
        function current()
        /**/
        {
            return $this->getItem($this->position);
        }
        
        /****m* iterator/key
         * SYNOPSIS
         */
        function key()
        /*
         * FUNCTION
         *      return key of current array entry
         * OUTPUTS
         *      (string) -- key of current array entry
         ****
         */
        {
            return $this->keys[$this->position];
        }
        
        /****m* iterator/next
         * SYNOPSIS
         */
        function next()
        /*
         * FUNCTION
         *      move to next entry
         ****
         */
        {
            ++$this->position;
        }
        
        /****m* iterator/rewind
         * SYNOPSIS
         */
        function rewind()
        /*
         * FUNCTION
         *      rewind array back to the start
         ****
         */
        {
            $this->position = 0;
        }
        
        /****m* iterator/valid
         * SYNOPSIS
         */
        function valid()
        /*
         * FUNCTION
         *      test if the current position is valid
         * OUTPUTS
         *      (bool) -- returns true for valid positions
         ****
         */
        {
            return ($this->position < $this->count);
        }
        
        /****m* iterator/seek
         * SYNOPSIS
         */
        function seek($position)
        /*
         * FUNCTION
         *      seek to position
         * INPUTS
         *      * $position (int) -- position to seek to
         ****
         */
        {
            $this->position = $position;
        }
        
        /****m* iterator/count
         * SYNOPSIS
         */
        public function count()
        /*
         * FUNCTION
         *      returns number of elements
         * OUTPUTS
         *      (int) -- number of elements of collection
         ****
         */
        {
            return $this->count;
        }
        
        /****m* iterator/getItem
         * SYNOPSIS
         */
        protected function getItem($pos)
        /*
         * FUNCTION
         *      return item for specified position
         * INPUTS
         *      * $pos (int) -- position to return item for
         * OUTPUTS
         *      (mixed) -- item value
         ****
         */
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
