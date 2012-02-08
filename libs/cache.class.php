<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core {
    /**
     * Cache core class.
     *
     * @octdoc      c:core/cache
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class cache
    /**/
    {
        /**
         * Standard caching backend.
         *
         * @octdoc  p:cache/$backend
         * @var     \org\octris\core\cache\storage_if
         */
        protected $backend;
        /**/

        /**
         * Fallback caching backend.
         *
         * @octdoc  p:cacbe/$fallback
         * @var     \org\octris\core\cache\storage_if|null
         */
        protected $fallback = null;
        /**/

        /**
         * Logger instance.
         *
         * @octdoc  p:cache/$logger
         * @var     \org\octris\core\logger $logger|null
         */
        protected $logger = null;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:cache/__construct
         * @param   \org\octris\core\cache\storage_if   $storage        Instance of cache storage backend.
         */
        public function __construct(\org\octris\core\cache\storage_if $storage)
        /**/
        {
            $this->backend = $storage;
        }

        /**
         * Set a fallback cache for example to combine a fast transient and a slower persistent cache,
         * the fallback would define the second, in this example persistent cache, that would be queried,
         * if the first cache does not contain the looked-up data.
         *
         * @octdoc  m:cache/setFallback
         * @param   \org\octris\core\cache\storage_if   $storage        Instance of cache storage backend.
         */
        public function setFallback(\org\octris\core\cache\storage_if $storage)
        /**/
        {
            $this->fallback = $storage;
        }

        /**
         * Set logger for logging problems and information with cache backends.
         *
         * @octdoc  m:cache/setLogger
         * @param   \org\octris\core\logger             $logger         Instance of logger class.
         */
        public function setLogger(\org\octris\core\logger $logger)
        /**/
        {
            $this->logger = $logger;
        }


    }
}
