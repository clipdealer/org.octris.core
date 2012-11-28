<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\net {
    /**
     * Helper class for temporarly storing request output data.
     *
     * @octdoc      c:net/buffer
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class buffer implements \Iterator
    /**/
    {
        /**
         * File handle for managing string stored in memory.
         *
         * @octdoc  p:buffer/$fh
         * @var     resource
         */
        protected $fh;
        /**/
        
        /**
         * Constructor.
         *
         * @octdoc  m:buffer/__construct
         */
        public function __construct()
        /**/
        {
            if (!($this->fh = fopen('php://memory', 'w'))) {
                throw new \Exception("unable to create io storage");
            }
        }
        
        /**
         * Destructor.
         *
         * @octdoc  m:buffer/__destruct
         */
        public function __destruct()
        /**/
        {
            fclose($this->fh);
        }
        
        /**
         * Store data.
         *
         * @octdoc  m:buffer/write
         * @param   resource            $ch             Curl resource handle.
         * @param   string              $data           Data to store.
         */
        public function write($ch, $data)
        /**/
        {
            fputs($this->fh, $data);
        }
        
        /**
         * Return stored data.
         *
         * @octdoc  m:buffer/getContent
         * @return  string                              Stored data.
         */
        public function getContent()
        /**/
        {
            rewind($this->fh);
            
            return stream_get_contents($fh);
        }
    }
}
