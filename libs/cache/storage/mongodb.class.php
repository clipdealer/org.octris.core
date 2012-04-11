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
     * MongoDB cache storage.
     *
     * @octdoc      c:storage/mongodb
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class mongodb extends \org\octris\core\cache\storage implements \org\octris\core\cache\storage_if, \IteratorAggregate
    /**/
    {
        /**
         * Instance of MongoDB database device.
         *
         * @octdoc  p:mongodb/$db
         * @var     \org\octris\core\db\mongodb
         */
        protected $db;
        /**/
        
        /**
         * Database connection.
         *
         * @octdoc  p:mongodb/$cn
         * @var     \org\octris\core\db\mongodb\connection
         */
        protected $cn;
        /**/

        /**
         * Namespace separator.
         *
         * @octdoc  p:mongodb/$ns_separator
         * @var     string
         */
        protected $ns_separator = '.';
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:mongodb/__construct
         * @param   \org\octris\core\db\mongodb     $db                     Instance of MongoDB database device.
         * @param   array                           $options                Cache options
         */
        public function __construct(\org\octris\core\db\mongodb $db, array $options)
        /**/
        {
            $this->db = $db;
            $this->cn = $db->getConnection();

            parent::__construct($options);

            $this->ns = 'caches' . $this->ns_separator . $this->ns;
        }

        /**
         * Make cache iteratable.
         *
         * @octdoc  m:mongodb/getIterator
         */
        public function getIterator()
        /**/
        {
            // TODO
        }

        /**
         * Compare and update a value. The value get's only updated, if the current value matches.
         *
         * @octdoc  m:mongodb/cas
         * @param   string          $key                    The key of the value to be updated.
         * @param   int             $v_current              Current stored value.
         * @param   int             $v_new                  New value to store.
         * @return  bool                                    Returns true, if the value was updated.
         */
        public function cas($key, $v_current, $v_new)
        /**/
        {
            return $this->cn->update(
                $this->ns, 
                array('key' => $key, 'value' => (int)$v_current),
                array('$set' => array('value' => (int)$v_new))
            );
        }

        /**
         * Increment a stored value
         *
         * @octdoc  m:mongodb/inc
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
         * @octdoc  m:mongodb/dec
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
         * Load a value from cache or create it from specified callback. In the latter case the created data returned by 
         * the callback will be stored in the cache.
         *
         * @octdoc  m:mongodb/load
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
         * @octdoc  m:mongodb/save
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
         * Remove a value from the cache.
         *
         * @octdoc  m:mongodb/remove
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
         * @octdoc  m:mongodb/clear
         */
        public function clear()
        /**/
        {
            // TODO
        }
    }
}
