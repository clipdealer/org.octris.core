<?php

namespace org\octris\core\app {
    use \org\octris\core\validate as validate;
    
    /****c* app/config
     * NAME
     *      config
     * FUNCTION
     *      handles application configuration
     * COPYRIGHT
     *      copyright (c) 2007-2010 by Harald Lapp
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

        /****v* config/$pathtypes
         * SYNOPSIS
         */
        protected static $pathtypes = array();
        /*
         * FUNCTION
         *      filled by constructor with the path types
         ****
         */

        /****v* config/$instance
         * SYNOPSIS
         */
        protected static $instance = NULL;
        /*
         * FUNCTION
         *      stores instance of lima_config object
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
        protected function __construct()
        /*
         * FUNCTION
         *      constructor -- setup several base configuration options, even before configuration file
         *      was loaded
         ****
         */
        {
            if (!$_ENV['OCTRIS_APP']->isSet || !$_ENV['OCTRIS_BASE']->isSet) {
                throw new Exception('unable to import OCTRIS_APP or OCTRIS_BASE!');
            }

            if (!$_ENV->validate('OCTRIS_APP', validate::T_ALPHANUM) || !$_ENV->validate('OCTRIS_BASE', validate::T_PRINT)) {
                throw new lima_exception_critical('unable to import OCTRIS_APP or OCTRIS_BASE - invalid settings!');
            } else {
                self::$data['common.app.name'] = $_ENV['OCTRIS_APP']->value;
                self::$data['common.app.base'] = $_ENV['OCTRIS_BASE']->value;
            }

            self::$data['common.app.development'] =
                (($_ENV->validate('OCTRIS_DEVEL', validate::T_BOOL)) &&        
                  $_ENV['OCTRIS_DEVEL']->value);
        }

        /****m* config/set
         * SYNOPSIS
         */
        static function set($name, $value)
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
        static function get($name)
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
        static function getSet($prefix)
        /*
         * FUNCTION
         *      return a set of configuration options
         * INPUTS
         *      * $prefix (string) -- prefix to search for
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
        static function getPath($type, $module = '')
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
            if (count(self::$pathtypes) == 0) {
                $class = new ReflectionClass(self);
                self::$pathtypes = array_flip($class->getConstants());
            }

            $return = '';

            if (isset(self::$pathtypes[$type])) {
                $return = sprintf(
                    $type,
                    self::$data['common.application.path'],
                    ($module 
                        ? $module 
                        : self::$data['common.application.name'])
                );
            }

            return realpath($return);
        }

        /****m* config/load
         * SYNOPSIS
         */
        static function load($app)
        /*
         * FUNCTION
         *      loads the configuration file(s) for specified application
         * INPUTS
         *      * $app (string) -- name of application to load config file(s) for
         ****
         */
        {
            self::$data = \org\octris\core::getProxy(
                'config',
                array(
                    self::$data['common.app.name'],
                    self::$data['common.app.base'],
                    self::$data['common.app.development']
                )
            )->load($app);
        }
    }
}