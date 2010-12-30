<?php

namespace org\octris\core {
    use \org\octris\core\app as app;
    use \org\octris\core\type\collection as collection;
    
    /****c* core/config
     * NAME
     *      config
     * FUNCTION
     *      handles application configuration
     * COPYRIGHT
     *      copyright (c) 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */

    class config {
        /****v* config/$instances
         * SYNOPSIS
         */
        private static $instances = array();
        /*
         * FUNCTION
         *      class instances
         ****
         */
        
        /****v* config/$data
         * SYNOPSIS
         */
        protected $data = array();
        /*
         * FUNCTION
         *      stores data of config (namespace)
         ****
         */

        /****v* config/$module
         * SYNOPSIS
         */
        protected $module = '';
        /*
         * FUNCTION
         *      name of module configuration belongs to
         ****
         */
        
        /****v* config/$name
         * SYNOPSIS
         */
        protected $name = '';
        /*
         * FUNCTION
         *      name of configuration file
         ****
         */
        
        /****m* config/__construct
         * SYNOPSIS
         */
        private function __construct($module, $name, $data) 
        /*
         * FUNCTION
         *      private constructor. Class instance may only be created by 
         *      static factory method 'load'.
         * INPUTS
         *      * $module (string) -- name of module configuration belongs to
         *      * $name (string) -- name of configuration file
         *      * $data (collection) -- configuration data
         ****
         */
        {
            $this->module = $module;
            $this->name   = $name;
            $this->data   = $data;
        }

        /****m* collection/defaults
         * SYNOPSIS
         */
        public function defaults(array $data)
        /*
         * FUNCTION
         *      set default values. values are only set, if not already present
         *      in collection.
         * INPUTS
         *      * $data (array) -- data to set
         ****
         */
        {
            $cfg = new collection($data);
            $cfg = $cfg->flatten();
            
            $this->data = $cfg->merge($this->data);
        }
        
        /****m* config/set
         * SYNOPSIS
         */
        public function set($name, $value)
        /*
         * FUNCTION
         *      sets an array of values
         * INPUTS
         *      * $name (string) -- name of property to set
         *      * $value (mixed) -- value to set for specified property
         ****
         */
        {
            $this->data[$name] = $value;
        }

        /****m* config/get
         * SYNOPSIS
         */
        public function get($name)
        /*
         * FUNCTION
         *      return value of spacified setting
         * INPUTS
         *      * $name (string) -- name of setting to return
         * OUTPUTS
         *      (string) -- returns setting for specified name
         ****
         */
        {
            $return = null;

            if (array_key_exists($name, $this->data)) {
                $return =& $this->data[$name];
            }

            return $return;
        }

        /****m* config/getSet
         * SYNOPSIS
         */
        public function getSet($prefix, $deflatten = false)
        /*
         * FUNCTION
         *      return a set of configuration options
         * INPUTS
         *      *   $prefix (string) -- prefix to search for
         *      *   $deflatten (bool) -- whether to deflatten result
         * OUTPUTS
         *      (array) -- set of matching options
         ****
         */
        {
            $prefix = rtrim($prefix, '.') . '.';

            $len = strlen($prefix);
            $set = array();

            foreach ($this->data as $k => $v) {
                if (substr($k, 0, $len) == $prefix) {
                    $set[substr($k, $len)] = $v;
                }
            }

            return $set;
        }

        /****m* config/save
         * SYNOPSIS
         */
        public function save($file = '')
        /*
         * FUNCTION
         *      save configuration file to destination. if destination is not
         *      specified, try to save in ~/config/<module>/<name>.yml
         * INPUTS
         *      * $file (string) -- (otional) destination to save configuration to
         ****
         */
        {
            if ($file == '') {
                $info = posix_getpwuid(posix_getuid());
                $file = $info['dir'] . '/.octris/' . $this->module . '/' . $this->name . '.yml';
            } else {
                $info = parse_url($file);
            }

            if (!isset($info['scheme'])) {
                $path = dirname($file);

                if (!is_dir($path)) {
                    mkdir($path, 0755, true);
                }
            }
            
            file_put_contents($file, yaml_emit($this->data->deflatten());
        }

        /****m* config/_load
         * SYNOPSIS
         */
        protected static function _load($module, $name)
        /*
         * FUNCTION
         *      actually load configuration file. the loader looks in the
         *      following places, loads the configuration file and merges
         *      them in specified lookup order:
         *
         *      *   ~/.octris/config.yml
         *      *   T_PATH_ETC/config.yml
         *      *   T_PATH_ETC/config_local.yml
         * INPUTS
         *      *   $module (string) -- name of module to laod configuration for
         *      *   $name (string) -- name of configuration file to load
         * OUTPUTS
         *      (collection) -- collection representation of the loaded
         *      configuration file
         *      (bool) -- false, if a configuration could not be loaded
         ****
         */
        {
            $cfg = new collection();
            $ret = false;

            // load default module config file
            $path = self::getPath(self::T_PATH_ETC, $module);
            $file = $path . '/' . $name . '.yml';
            
            if (is_readable($file) && ($tmp = yaml_parse_file($file)) && !is_null($tmp)) {
                $tmp = new collection($tmp);
                $cfg->merge($tmp->flatten());
                
                $ret = true;
            }

            // load local config file
            $file = $path . '/' . $name . '_local.yml';
            
            if (is_readable($file) && ($tmp = yaml_parse_file($file)) && !is_null($tmp)) {
                $tmp = new collection($tmp);
                $cfg->merge($tmp->flatten());
                
                $ret = true;
            }
        
            // load global framework configuration
            $info = posix_getpwuid(posix_getuid());
            $file = $info['dir'] . '/.octris/' . $module . '/' . $name . '.yml';
            
            if (is_readable($file) && ($tmp = yaml_parse_file($file)) && !is_null($tmp)) {
                $tmp = new collection($tmp);
                $cfg->merge($tmp->flatten());
                
                $ret = true;
            }

            return array($cfg, $ret);
        }

        /****m* config/load
         * SYNOPSIS
         */
        public static function load($module = '', $name = 'config')
        /*
         * FUNCTION
         *      Loads the configuration file(s) for app configured in 
         *      the environment variable OCTRIS_APP or for module specified
         *      by a parameter.
         * 
         *      Factory pattern -- loads each configuration file only one time.
         * INPUTS
         *      * $module (string) -- (optional) name of module to load config file(s) for
         *      * $name (string) -- (optional) name of configuration file to load
         * OUTPUTS
         *      (config) -- instance of configuration class
         *      (bool) -- false, if configuration file could not be loaded.
         ****
         */
        {
            $module   = ($module == '' ? $_ENV['OCTRIS_APP']->value : $module);
            $key      = md5($module . '|' . $name);
            $instance = (isset(self::$instances[$key])
                         ? self::$instances[$key]
                         : null);
            
            if ($instance == null) {
                list($cfg, $err) = self::_load($module, $name);
                
                if ($err !== false) {
                    $instance = self::$instances[$key] = new static($module, $name, $cfg);
                }
            }
            
            return array($instance, $err);
        }
    }
}
