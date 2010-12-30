<?php

namespace org\octris\core\config {
    /****c* config/filter
     * NAME
     *      filter
     * FUNCTION
     *      implements FilterInterator for filtering configuration
     * COPYRIGHT
     *      copyright (c) 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */
     
    class filter extends \FilterIterator {
        /****v* filter/$prefix
         * SYNOPSIS
         */
        private $prefix = '';
        /*
         * FUNCTION
         *      prefix to use as filter
         ****
         */
        
        /****m* filter/__construct
         * SYNOPSIS
         */
        public function __construct(Iterator $iterator, $prefix)
        /*
         * FUNCTION
         *      constructor
         * INPUTS
         *      * $iterator (Iterator) -- iterator of collection to filter
         *      * $prefix (string) -- prefix to filter for
         ****
         */
        {
            parent::__construct($iterator);

            $this->prefix = rtrim($prefix, '.') . '.';
            $this->rewind();
        }

        /****m* filter/getArrayCopy
         * SYNOPSIS
         */
        public function getArrayCopy($clean = false)
        /*
         * FUNCTION
         *      get copy of filtered array
         * INPUTS
         *      * $clean (bool) -- if true, remote prefix from keys
         * OUTPUTS
         *      (array) -- filtered array
         ****
         */
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

        /****m* filter/accept
         * SYNOPSIS
         */
        public function accept()
        /*
         * FUNCTION
         *      filter implementation
         * OUTPUTS
         *      (bool) -- returns true, if element should be part of result
         ****
         */
        {
            return (substr($this->key(), 0, strlen($this->prefix)) == $this->prefix);
        }
    }
}
