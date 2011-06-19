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
         * Constructor.
         *
         * @octdoc  m:result/__construct
         * @param   MongoCursor         $cursor             Cursor resource from MongoDB.
         */
        public function __construct($cursor)
        /**/
        {
            $this->cursor = $cursor;
        }

        /**
         * Fetch next result.
         *
         * @octdoc  m:result/fetchNext
         * @return  bool|array                              Returns array of data or false, if no more data is available.
         */
        public function fetchNext()
        /**/
        {
            $data = false;

            if (is_object($this->cursor) && $this->cursor->hasNext()) {
                $data = $this->cursor->getNext();
            }
        
            return $data;
        }
    }
}
