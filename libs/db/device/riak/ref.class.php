<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\db\device\riak {
    /**
     * Bucket / link reference.
     *
     * @octdoc      c:riak/ref
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     *
     * @todo        Allow cross-device links (riak -> mysql, etc.)?
     */
    class ref
    /**/
    {
        /**
         * Name of bucket to reference to.
         *
         * @octdoc  p:ref/$bucket
         * @var     string
         */
        protected $bucket;
        /**/
        
        /**
         * Key to reference to.
         *
         * @octdoc  p:ref/$key
         * @var     string
         */
        protected $key;
        /**/
        
        /**
         * Constructor.
         *
         * @octdoc  m:ref/__construct
         * @param   string          $bucket             Name of bucket to link to.
         * @param   string          $key                Key in bucket to link to.
         */
        public function __construct($bucket, $key)
        /**/
        {
            $this->bucket = $bucket;
            $this->key    = $key;
        }
        
        /**
         * Return reference property.
         *
         * @octdoc  m:ref/__get
         * @param   string          $name               Name of property to return value of.
         */
        public function __get($name)
        /**/
        {
            return (isset($this->{$name})
                    ? $this->{$name}
                    : null);
        }
    }
}
