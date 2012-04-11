<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\cache {
    /**
     * Cache storage base class.
     *
     * @octdoc      c:cache/storage
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    abstract class storage implements \IteratorAggregate
    /**/
    {
        /**
         * Storage namespace.
         *
         * @octdoc  p:storage/$ns
         * @var     string
         */
        protected $ns = '';
        /**/
        
        /**
         * Namespace separator.
         *
         * @octdoc  p:storage/$ns_separator
         * @var     string
         */
        protected $ns_separator = ':';
        /**/

        /**
         * Time to live in seconds.
         *
         * @octdoc  p:storage/$ttl
         * @var     int
         */
        protected $ttl = 0;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:storage/__construct
         * @param   array           $options                Cache options
         */
        public function __construct(array $options)
        /**/
        {
            if (isset($options['ns_separator'])) {
                $this->ns_separator = $options['ns_separator'];
            }
            if (isset($options['ns'])) {
                $this->ns = $options['ns'] . $this->ns_separator;
            }
            if (isset($options['ttl'])) {
                $this->ttl = $options['ttl'];
            }
        }
    }
}
