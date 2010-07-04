<?php

namespace org\octris\core {
    use cache\proxy as proxy;
    
    /****c* core/cache
     * FUNCTION
     *      implements a caching mechanism intended to use for caching
     *      data result of methods (eg.: result sets of db queries).
     * COPYRIGHT
     *      copyright (c) 2006-2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald.lapp@gmail.com>
     ****
     */
 
    lima_cache::addCache(new lima_cache_request());

    class cache { 
        /****v* cache/$caches
         * SYNOPSIS
         */
        static protected $caches = array();
        /*
         * FUNCTION
         *      caches available
         ****
         */
    
        /****v* cache/$strategies
         * SYNOPSIS
         */
        static protected $strategies = array();
        /*
         * FUNCTION
         *      registered cache strategies
         ****
         */
    
        /****m* cache/__construct
         * SYNOPSIS
         */
        protected function __construct() 
        /*
         * FUNCTION
         *      prevent 
         ****
         */
        {
        }

        /****m* cache/addCache
         * SYNOPSIS
         */
        static function addCache($cache)
        /*
         * FUNCTION
         *      add instance of cache backend
         * INPUTS
         *      * $cache (object) -- instance of cache object
         * OUTPUTS
         *      (object) -- instance of cache object specified as first parameter
         ****
         */
        {
            $class = get_class($cache);
            self::$caches[$class] = $cache;
        
            return $cache;
        }

        /****m* cache/getProxy
         * SYNOPSIS
         */
        public static function getProxy($object, $type, array $options = array())
        /*
         * FUNCTION
         *      object proxy cache
         * INPUTS
         *      * $object (object) -- instance of any object to cache
         *      * $type (string) -- cache type to use
         *      * $options (array) -- (optional) settings for cache
         ****
         */
        {
            $cache = (isset(self::$caches[$type])
                      ? self::$caches[$type]
                      : self::$caches[self::T_REQUEST]);
                  
            $return = new proxy($cache, $options, $object);
        
            return $return;
        }

        /****m* cache/getInstance
         * SYNOPSIS
         */
        static function getInstance($type, array $params = NULL) 
        /*
         * FUNCTION
         *      returns (new) instance of cache object. for each cache type only one instance will
         *      be created!
         * INPUTS
         *      * $type (string) -- cache type to use
         *      * $params (array) -- (optional) parameters for setting up cache instance
         * OUTPUTS
         *      (object) -- instance of cache object
         ****
         */
        {
            $cache = (isset(self::$caches[$type])
                      ? self::$caches[$type]
                      : self::$caches[self::T_REQUEST]);
                  
            return $cache;
        }
    }
