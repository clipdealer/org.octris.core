<?php

namespace org\octris\core {
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
        /****d* config/T_PATH_CACHE, T_PATH_DATA, T_PATH_ETC, T_PATH_HOST, T_PATH_LIBS, T_PATH_LIBSJS, T_PATH_LOCALE, T_PATH_RESOURCES, T_PATH_STYLES, T_PATH_LOG, T_PATH_WORK, T_PATH_WORK_LIBSJS, T_PATH_WORK_RESOURCES, T_PATH_WORK_STYLES, T_PATH_WORK_TPL
         * SYNOPSIS
         */
        const T_PATH_CACHE          = '%s/cache/%s';
        const T_PATH_DATA           = '%s/data/%s';
        const T_PATH_ETC            = '%s/etc/%s';
        const T_PATH_HOST           = '%s/host/%s';
        const T_PATH_LIBS           = '%s/libs/%s';
        const T_PATH_LIBSJS         = '%s/host/%s/libsjs';
        const T_PATH_LOCALE         = '%s/locale/%s';
        const T_PATH_LOG            = '%s/log/%s';
        const T_PATH_RESOURCES      = '%s/host/%s/resources';
        const T_PATH_STYLES         = '%s/host/%s/styles';
        const T_PATH_TOOLS          = '%s/tools/%s';
        const T_PATH_WORK           = '%s/work/%s';
        const T_PATH_WORK_LIBS      = '%s/work/%s/libs';
        const T_PATH_WORK_LIBSJS    = '%s/work/%s/libsjs';
        const T_PATH_WORK_RESOURCES = '%s/work/%s/resources';
        const T_PATH_WORK_STYLES    = '%s/work/%s/styles';
        const T_PATH_WORK_TPL       = '%s/work/%s/templates';
        /*
         * FUNCTION
         *      used in combination with app/getPath to determine path
         ****
         */

        /****v* config/$data
         * SYNOPSIS
         */
        protected static $data = array();
        /*
         * FUNCTION
         *      stores data of config (namespace)
         ****
         */

        /****m* config/__construct
         * SYNOPSIS
         */
        private function __construct() {}
        private function __clone() {}
        /*
         * FUNCTION
         *      private to make class static
         ****
         */

        /****m* config/set
         * SYNOPSIS
         */
        public static function set($name, $value)
        /*
         * FUNCTION
         *      sets an array of values
         * INPUTS
         *      * $name (string) -- name of property to set
         *      * $value (mixed) -- value to set for specified property
         ****
         */
        {
            self::$data[$name] = $value;
        }

        /****m* config/get
         * SYNOPSIS
         */
        public static function get($name)
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

            if (array_key_exists($name, self::$data)) {
                $return =& self::$data[$name];
            }

            return $return;
        }

        /****m* config/getSet
         * SYNOPSIS
         */
        public static function getSet($prefix, $deflatten = false)
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

            foreach (self::$data as $k => $v) {
                if (substr($k, 0, $len) == $prefix) {
                    $set[substr($k, $len)] = $v;
                }
            }

            return $set;
        }

        /****m* config/getPath
         * SYNOPSIS
         */
        public static function getPath($type, $module = '')
        /*
         * FUNCTION
         *      returns path for specified type for current application
         * INPUTS
         *      * $type (string) -- type of path to return
         *      * $module (string) -- (optional) name of module to return path for. default is: current application name
         * OUTPUTS
         *      (string) -- existing path or empty string, if path does not exist
         ****
         */
        {
            $return = sprintf(
                $type,
                self::$data['common.app.path'],
                ($module 
                    ? $module 
                    : self::$data['common.app.name'])
            );

            return realpath($return);
        }

        /****m* config/_load
         * SYNOPSIS
         */
        protected static function _load($module)
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
         * OUTPUTS
         *      (mixed) -- either a collection representation of the loaded
         *      configuration file or false, if a configuration could not be loaded
         ****
         */
        {
            $module = ($module == '' ? $_ENV['OCTRIS_APP']->value : $module);
            
            self::$data['common.app.name']  = $module;
            self::$data['common.app.base']  = $_ENV['OCTRIS_BASE']->value;
            self::$data['common.app.devel'] = $_ENV['OCTRIS_DEVEL']->value;

            $cfg = new collection();
            $ret = false;

            // load global framework configuration
            $info = posix_getpwuid(posix_getuid());
            $file = $info['dir'] . '/.octris/config.yml';
            
            if (is_readable($file) && ($tmp = yaml_parse_file($file)) && !is_null($tmp)) {
                $tmp = new collection($tmp);
                $cfg->merge($tmp->flatten());
                
                $ret = true;
            }

            // load default module config file
            $path = self::getPath(self::T_PATH_ETC, $module);
            $file = $path . '/config.yml';
            
            if (is_readable($file) && ($tmp = yaml_parse_file($file)) && !is_null($tmp)) {
                $tmp = new collection($tmp);
                $cfg->merge($tmp->flatten());
                
                $ret = true;
            }

            // load local config file
            $file = $path . '/config.yml';
            
            if (is_readable($file) && ($tmp = yaml_parse_file($file)) && !is_null($tmp)) {
                $tmp = new collection($tmp);
                $cfg->merge($tmp->flatten());
                
                $ret = true;
            }
        
            return ($ret ? $cfg : false);
        }

        /****m* config/load
         * SYNOPSIS
         */
        public static function load($module = '')
        /*
         * FUNCTION
         *      Loads the configuration file(s) for app configured in 
         *      the environment variable OCTRIS_APP or for module specified
         *      by a parameter.
         * INPUTS
         *      * $module (string) -- (optional) name of module to load config file(s) for
         * OUTPUTS
         *      (bool) -- returns true, if config file was load successful
         ****
         */
        {
            if (($cfg = self::_load($module)) !== false) {
                self::$data = $cfg;
            }
        }
    }
}
