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
        protected $data = array();
        /*
         * FUNCTION
         *      stores data of config (namespace)
         ****
         */

        /****m* config/T_PATH_CACHE, T_PATH_DATA, T_PATH_ETC, T_PATH_HOST, T_PATH_LIBS, T_PATH_LIBSJS, T_PATH_LOCALE, T_PATH_RESOURCES, T_PATH_STYLES, T_PATH_LOG, T_PATH_WORK, T_PATH_WORK_LIBSJS, T_PATH_WORK_RESOURCES, T_PATH_WORK_STYLES, T_PATH_WORK_TPL
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
         *      used in combination with config/getPath to determine path
         ****
         */

        /****v* config/$pathtypes
         * SYNOPSIS
         */
        protected $pathtypes = array();
        /*
         * FUNCTION
         *      filled by constructor with the path types
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
                $this->data['common.app.name'] = $_ENV['OCTRIS_APP']->value;
                $this->data['common.app.base'] = $_ENV['OCTRIS_BASE']->value;
            }

            $this->data['common.app.development'] =
                (($_ENV->validate('OCTRIS_DEVEL', validate::T_BOOL)) &&        
                  $_ENV['OCTRIS_DEVEL']->value);

            $class = new ReflectionClass($this);
            $this->pathtypes = array_flip($class->getConstants());
        }

        /****m* config/getInstance
         * SYNOPSIS
         */
        static function getInstance()
        /*
         * FUNCTION
         *      creates new instance of config object.
         ****
         */
        {
            if (is_null(self::$instance)) {
                self::$instance = new config();
            }

            return self::$instance;
        }

        /****m* config/set
         * SYNOPSIS
         */
        function set($name, $value)
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
        function get($name)
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
            $return = NULL;

            if (array_key_exists($name, $this->data)) {
                $return =& $this->data[$name];
            }

            return $return;
        }

        /****m* config/getSet
         * SYNOPSIS
         */
        function getSet($prefix)
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

            foreach ($this->data as $k => $v) {
                if (substr($k, 0, $len) == $prefix) {
                    $set[substr($k, $len)] = $v;
                }
            }

            return $set;
        }

        /****m* config/getPath
         * SYNOPSIS
         */
        function getPath($type, $appname = '')
        /*
         * FUNCTION
         *      returns path for specified type for current application
         * INPUTS
         *      * $type (string) -- type of path to return
         *      * $appname (string) -- optional name of application to return path for. default is: current application
         * OUTPUTS
         *      (string) -- existing path or empty string, if path does not exist
         ****
         */
        {
            if (isset($this->pathtypes[$type])) {
                $return = sprintf(
                    $type,
                    $this->get('common.application.path'),
                    ($appname ? $appname : $this->get('common.application.name'))
                );
            } else {
                $return = '';
            }

            return realpath($return);
        }

        /****m* config/load
         * SYNOPSIS
         */
        function load($app)
        /*
         * FUNCTION
         *      loads the configuration file(s) for specified application
         * INPUTS
         *      * $app (string) -- name of application to load config file(s) for
         ****
         */
        {
            $this->data = \org\octris\core::getProxy(
                'config',
                array(
                    $this->data['common.app.name'],
                    $this->data['common.app.base'],
                    $this->data['common.app.development']
                )
            )->load($app);
        }
    }
}