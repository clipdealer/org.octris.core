<?php

namespace org\octris\core\cache {
    /****c* cache/proxy
     * NAME
     *      lima_cache_proxy
     * FUNCTION
     *      lima_cache_proxy provides the proxy functionality that is required to 
     *      save resultsets of a datasource to the cache and retrieve result-
     *      sets from cache 
     * COPYRIGHT
     *      copyright 2007-2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */

    class proxy {
        /****v* proxy/$default_lifetime
         * SYNOPSIS
         */
        protected $default_lifetime = 86400;
        /*
         * FUNCTION
         *      default lifetime of cache in production environment:
         *
         *      * lifetime > 0 -- the lifetime in seconds
         *      * lifetime = 0 -- the data doesn't expire
         *      * lifetime < 0 -- data should not be cached
         ****
         */
    
        /****v* proxy/$caches
         * SYNOPSIS
         */
        protected $cache;
        /*
         * FUNCTION
         *      stores instance of lima_cache object to use for proxy
         ****
         */

        /****v* proxy/$object
         * SYNOPSIS
         */
        protected $object;
        /*
         * FUNCTION
         *      instance of object that is accessed through proxy
         ****
         */

        /****v* proxy/$class
         * SYNOPSIS
         */
        protected $class;
        /*
         * FUNCTION
         *      class of object that is accessed through proxy
         ****
         */

        /****v* proxy/$options
         * SYNOPSIS
         */
        protected $options = array();
        /*
         * FUNCTION
         *      cache options
         ****
         */

        /****m* proxy/__construct
         * SYNOPSIS
         */
        public function __construct($cache, array $options, $object) 
        /*
         * FUNCTION
         *      constructor
         * INPUTS
         *      * $cache (object) -- instance of cache object to use with proxy
         *      * $options (array) -- caching options
         *      * $object (object) -- object that is accessed through proxy
         ****
         */
        {
            $this->cache  = $cache;
            $this->object = $object;
            $this->class  = get_class($this->object);
        
            $this->options = array(
                'lifetime'  => (isset($options['lifetime']) 
                                ? $options['lifetime']
                                : $this->default_lifetime),
                'cachekey'  => (isset($options['cachekey'])
                                ? $options['cachekey']
                                : '')
            );
        }

        /****m* proxy/__call
         * SYNOPSIS
         */
        public function __call($name, $params = array()) 
        /*
         * FUNCTION
         *      method of datasource to call through proxy
         ****
         */
        {
            if (is_callable($this->options['cachekey'])) {
                $key = $this->options['cachekey']($this->class, $name, $params);
            } else {
                $key = $this->getCacheKey($this->class, $name, $params, $this->options['cachekey']);
            }
        
            $lifetime = $this->options['lifetime'];
    
            $data = array();
    
            if ($lifetime < 0 || !$this->getCached($this->class, $key, $data)) {
                $data = call_user_func_array(array($this->object, $name), $params);

                if ($lifetime >= 0) {
                    $this->cache->put($this->class, $key, $data, $lifetime);
                }
            }
        
            return $data;
        }

        /****m* proxy/getCacheKey
         * SYNOPSIS
         */
        protected function getCacheKey($classname, $method, $params, $custom) 
        /*
         * FUNCTION
         *      default method to return a key for a specific cached data, when object to cache
         *      does not implement it's own getCacheKey method. the key is generated from
         *      provided classname, the called method and the parameter of the method
         *      and returned as md5 hash. through the specified parameters it's 
         *      possible to generate all kind of cache keys - even user specific cache
         *      keys, if a user_id is provided.
         * INPUTS
         *      * $classname (string) -- name of class to cache data of
         *      * $method (string) -- class method to cache data of
         *      * $params (array) -- parameters to include in cache key
         * OUTPUTS
         *      (string) -- generated cash key (md5 hash)
         ****
         */
        {
            $key = md5(serialize($params) . '-' . $classname . '-' . $method . '-' . $custom);
    
            return $key;
        }
    
        /****m* proxy/getCached
         * SYNOPSIS
         */
        public function getCached($classname, $key, &$data)
        /*
         * FUNCTION 
         *      this method will return data from local (request based) cache or
         *      cache instance. all data from cache instance will be cached locally 
         *      to avoid multiple connections to cache instance backend
         ****
         */
        {
            static $cached = array();
        
            $return = true;
        
            if (array_key_exists($key, $cached)) {
                $data = $cached[$key];
            } else {
                $return = $this->cache->get($classname, $key, $data);
            
                if ($return) {
                    $cached[$key] = $data;
                }
            }
        
            return $return;
        }
    }
}
