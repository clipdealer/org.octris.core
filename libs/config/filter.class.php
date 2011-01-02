<?php

namespace org\octris\core\config {
    /**
     * class: config/filter
     *
     * implements FilterInterator for filtering configuration
     * 
     * @copyright   copyright (c) 2010 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     **
     */
     
    class filter extends \FilterIterator {
        /**
         * property: filter/$prefix
         *
         * prefix to use as filter
         */
        private $prefix = '';
        /**/
        
        /**
         * method: filter/__construct
         *
         * constructor
         *
         * @param   Iterator    $iterator   iterator of collection to filter
         * @param   string      $prefix     prefix to filter for
         */
        public function __construct(Iterator $iterator, $prefix)
        /**/
        {
            parent::__construct($iterator);

            $this->prefix = rtrim($prefix, '.') . '.';
            $this->rewind();
        }

        /**
         * method: filter/getArrayCopy
         *
         * get copy of filtered array
         *
         * @param   bool    $clean      if true, remote prefix from keys
         * @return  array               filtered array
         */
        public function getArrayCopy($clean = false)
        /**/
        {
            $this->rewind();

            $data = array();
            
            if ($clean) {
                $len = strlen($this->prefix);
                
                foreach ($this as $k => $v) {
                    $data[substr($k, $len)] = $v;
                }
            } else {
                foreach ($this as $k => $v) {
                    $data[$k] = $v;
                }
            }

            $this->rewind();
            
            return $data;
        }

        /**
         * method: filter/accept
         *
         * filter implementation
         *
         * @return  bool        returns true, if element should be part of result
         */
        public function accept()
        /**/
        {
            return (substr($this->key(), 0, strlen($this->prefix)) == $this->prefix);
        }
    }
}
