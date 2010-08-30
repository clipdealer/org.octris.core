<?php

namespace org\octris\core\tpl\type\collection {
    /****c* collection/iterator
     * NAME
     *      iterator
     * FUNCTION
     *      implements functionality for iterating a tpl-type collection
     * COPYRIGHT
     *      copyright (c) 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */

    class iterator implements \Iterator, \SeekableIterator, \Countable {
        /****v* iterator/$position
         * SYNOPSIS
         */
        protected $position = 0;
        /*
         * FUNCTION
         *      iterator position
         ****
         */
        
        /****v* iterator/$keys
         * SYNOPSIS
         */
        protected $keys = array();
        /*
         * FUNCTION
         *      keys of data
         ****
         */
        
        /****v* iterator/$data
         * SYNOPSIS
         */
        protected $data = array();
        /*
         * FUNCTION
         *      iterator data
         ****
         */
        
        /****v* iterator/$count
         * SYNOPSIS
         */
        protected $count = 0;
        /*
         * FUNCTION
         *      number of items
         ****
         */
        
        /****m* iterator/__construct
         * SYNOPSIS
         */
        function __construct($data)
        /*
         * FUNCTION
         *      constructor
         * INPUTS
         *      * $data (mixed) -- data for iterating over
         ****
         */
        {
            $this->position = 0;
            $this->keys     = array_keys($data);
            $this->data     = $data;
            $this->count    = count($this->keys);
        }
        
        /****m* iterator/current
         * SYNOPSIS
         */
        function current()
        /*
         * FUNCTION
         *      return current array entry
         * OUTPUTS
         *      (mixed) -- value of current entry
         ****
         */
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
