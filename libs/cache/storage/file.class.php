<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\cache\storage {
    /**
     * Filesystem cache storage.
     *
     * @octdoc      c:storage/file
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class file extends \org\octris\core\cache\storage
    /**/
    {
        /**
         * Hash algorithm
         *
         * @octdoc  p:file/$hash_algo
         * @var     string
         */
        protected $hash_algo = 'adler32';
        /**/
        
        /**
         * Namespace separator.
         *
         * @octdoc  p:file/$ns_separator
         * @var     string
         */
        protected $ns_separator = '/';
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:file/__construct
         * @param   array           $options                Optional cache options.
         */
        public function __construct(array $options = array())
        /**/
        {
            parent::__construct($options);
        }

        /**
         * Make cache iteratable.
         *
         * @octdoc  m:file/getIterator
         */
        public function getIterator()
        /**/
        {
            // TODO
        }

        /**
         * Compare and update a value. The value get's only updated, if the current value matches.
         *
         * @octdoc  m:file/cas
         * @param   string          $key                    The key of the value to be updated.
         * @param   int             $v_current              Current stored value.
         * @param   int             $v_new                  New value to store.
         * @return  bool                                    Returns true, if the value was updated.
         */
        public function cas($key, $v_current, $v_new)
        /**/
        {
            // TODO
        }

        /**
         * Increment a stored value
         *
         * @octdoc  m:file/inc
         * @param   string          $key                    The key of the value to be incremented.
         * @param   int             $step                   The step that the value should be incremented by.
         * @param   bool            $success                Optional parameter that returns true, if the update succeeded.
         * @return  int                                     The updated value.
         */
        public function inc($key, $step, &$success = null)
        /**/
        {
            // TODO
        }

        /**
         * Decrement a stored value.
         *
         * @octdoc  m:file/dec
         * @param   string          $key                    The key of the value to be decremented.
         * @param   int             $step                   The step that the value should be decremented by.
         * @param   bool            $success                Optional parameter that returns true, if the update succeeded.
         * @return  int                                     The updated value.
         */
        public function dec($key, $step, &$success = null)
        /**/
        {
            // TODO
        }

        /**
         * Fetch data from cache without populating the cache, if no data is stored for specified id.
         *
         * @octdoc  m:file/fetch
         * @param   string          $key                    The key of the value to fetch.
         * @param   bool            $success                Optional parameter that returns true, if the fetch succeeded.
         * @return  mixed                                   The data stored in the cache.
         */
        public function fetch($key, &$success = null)
        /**/
        {
            // TODO
        }

        /**
         * Load a value from cache or create it from specified callback. In the latter case the created data returned by 
         * the callback will be stored in the cache.
         *
         * @octdoc  m:file/load
         * @param   string          $key                    The key of the value to be loaded.
         * @param   callable        $cb                     Callback to call if the key is not found in the cache.
         * @param   int             $ttl                    Optional ttl. Uses the configured ttl if not specified.
         * @return  mixed                                   Stored data.
         */
        public function load($key, callable $cb, $ttl = null)
        /**/
        {
            // TODO
        }

        /**
         * Store a value to the cache.
         *
         * @octdoc  m:file/save
         * @param   string          $key                    The key the value should be stored in.
         * @param   mixed           $data                   Arbitrary (almost) data to store.
         * @param   int             $ttl                    Optional ttl. Uses the configured ttl if not specified.
         */
        public function save($key, $data, $ttl = null)
        /**/
        {
            // TODO
        }

        /**
         * Checks if a key exists in the cache.
         *
         * @octdoc  m:file/exists
         * @param   string          $key                    The key to test.
         * @return  bool                                    Returns true if the key exists, otherwise false.
         */
        public function exists($key)
        /**/
        {
            // TODO
        }

        /**
         * Remove a value from the cache.
         *
         * @octdoc  m:file/remove
         * @param   string          $key                    The key of the value that should be removed.
         */
        public function remove($key)
        /**/
        {
            // TODO
        }

        /**
         * Clear the entire cache.
         *
         * @octdoc  m:file/clear
         */
        public function clear()
        /**/
        {
            // TODO
        }
    }
}
