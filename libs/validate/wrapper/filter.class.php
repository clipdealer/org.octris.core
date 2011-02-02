<?php

namespace org\octris\core\validate\wrapper {
    /**
     * Implements FilterIterator for filtering a wrapper.
     * 
     * @octdoc      c:wrapper/filter
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     **
     */
     
    class filter extends \FilterIterator {
        /**
         * Prefix to use as filter.
         *
         * @octdoc  v:filter/$prefix
         * @var     string
         */
        private $prefix = '';
        /**/
        
        /**
         * Constructor.
         *
         * @octdoc  m:filter/__construct
         * @param   Iterator    $iterator   Iterator of collection to filter.
         * @param   string      $prefix     Prefix to filter for.
         */
        public function __construct(Iterator $iterator, $prefix)
        /**/
        {
            parent::__construct($iterator);

            $this->prefix = $prefix;
            $this->rewind();
        }

        /**
         * Filter implementation.
         *
         * @octdoc  m:filter/accept
         * @return  bool        Returns TRUE, if element should be part of result.
         */
        public function accept()
        /**/
        {
            return (substr($this->key(), 0, strlen($this->prefix)) == $this->prefix);
        }
    }
}
