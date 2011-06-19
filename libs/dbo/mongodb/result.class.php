<?php

namespace org\octris\core\dbo\mongodb {
    /**
     * Result set of a mongodb query.
     *
     * @octdoc      c:mongodb/result
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     * @todo        Implement collection / Array access
     */
    class result extends \org\octris\core\type\collection
    /**/
    {
        /**
         * MongoDB cursor.
         *
         * @octdoc  v:result/$cursor
         * @var     MongoCursor
         */
        private $cursor = null;
        /**/

        /**
         * Collection name.
         *
         * @octdoc  v:result/$collection
         * @var     string
         */
        protected $collection = '';
        /**/
        
        /**
         * Constructor.
         *
         * @octdoc  m:result/__construct
         * @param   string              $collection         Name of result object to create instance for result item.
         * @param   MongoCursor         $cursor             Cursor resource from MongoDB.
         */
        public function __construct($collection, MongoCursor $cursor)
        /**/
        {
            $this->collection = $collection;
            $this->cursor     = $cursor;
            $this->count      = count($this->cursor);
        }

        /** Iterator **/
        
        /**
         * Counts the number of results for this query.
         *
         * @octdoc  m:result/count
         * @return  int                     Number of results.
         */
        public function count()
        /**/
        {
            return count($this->cursor);
        }
        
        /**
         * Returns the current element.
         *
         * @octdoc  m:result/current
         * @return  \org\octris\core\dbo\mongodb\object     Result.   
         */
        public function current()
        /**/
        {
            $class = $this->collection;
            
            return new $class($this->cn, current($this->cursor));
        }
        
        /**
         * Returns the current result's _id.
         *
         * @octdoc  m:result/key
         * @return  string                  _id of current result row.
         */
        public function key()
        /**/
        {
            return key($this->cursor);
        }

        /**
         * Move forward to next element.
         *
         * @octdoc  m:result/next
         */
        public function next()
        /**/
        {
            ++$this->position;
            next($this->cursor);
        }
        
        /**
         * Rewind the cursor to the first element.
         *
         * @octdoc  m:result/rewind
         */
        public function rewind()
        /**/
        {
            $this->position = 0;
            reset($this->cursor);
        }

        /**
         * Checks if the cursor is reading a valid result.
         *
         * @octdoc  m:result/valid
         * @return  bool                    Returns true, if result is valid.
         */
        public function valid()
        /**/
        {
            $this->cursor->valid();
        }

        /** Special collection functionality **/

        /**
         * Return current position of iterator.
         *
         * @octdoc  m:collection/getPosition
         * @return  int                     Iterator position.   
         */
        public function getPosition()
        /**/
        {
            return $this->position;
        }

        /** Methods (currenlty) not implemented needs to overwrite parent implementations **/
        
        public function prev() {
            throw new Exception(__METHOD__ . ' not yet implemented!');
        }
        public function seek($position) {
            throw new Exception(__METHOD__ . ' not yet implemented!');
        }
        public function offsetGet($offs) {
            throw new Exception(__METHOD__ . ' not yet implemented!');
        }
        public function offsetSet($offs, $value) {
            throw new Exception(__METHOD__ . ' not yet implemented!');
        }
        public function offsetExists($offs) {
            throw new Exception(__METHOD__ . ' not yet implemented!');
        }
        public function offsetUnset($offs) {
            throw new Exception(__METHOD__ . ' not yet implemented!');
        }
        public function unserialize($data) {
            throw new Exception(__METHOD__ . ' not yet implemented!');
        }
        public function getArrayCopy() {
            throw new Exception(__METHOD__ . ' not yet implemented!');
        }
        public function exchangeArray($value) {
            throw new Exception(__METHOD__ . ' not yet implemented!');
        }
    }
}
