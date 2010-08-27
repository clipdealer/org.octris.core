<?php

namespace org\octris\core\data
    /****c* data/config
     * NAME
     *      config
     * FUNCTION
     *      loads (yaml) configuration files
     * COPYRIGHT
     *      copyright (c) 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */

    require_once('base/libs/lima_ds/lima_ds_yaml.class.php');
    require_once('base/libs/lima_type/lima_type_array.class.php');

    class config extends lima_ds_yaml {
        /****v* get_config/$cache_lifetime
         * SYNOPSIS
         */
        protected $cache_lifetime     = 0;
        protected $cache_lifetime_dev = -1;
        /*
         * FUNCTION
         *      in production mode:     cache forever
         *      in development mode:    deactivate cache
         ****
         */
 
        /****v* get_config/$cache_type
         * SYNOPSIS
         */
        protected $cache_type = lima_cache::T_FILE;
        /*
         * FUNCTION
         *      default cache type. the default cache type can be overwritten 
         *      through a subclass
         ****
         */
 
        /****v* get_config/$lima_app
         * SYNOPSIS
         */
        protected $lima_app = '';
        /*
         * FUNCTION
         *      stores name of application
         ****
         */
     
        /****v* get_config/$lima_base
         * SYNOPSIS
         */
        protected $lima_base = '';
        /*
         * FUNCTION
         *      stores basepath of application
         ****
         */
     
        /****v* get_config/$lima_devel
         * SYNOPSIS
         */
        protected $lima_devel = false;
        /*
         * FUNCTION
         *      stores, if application is in development mode
         ****
         */
    
        /****m* get_config/__construct
         * SYNOPSIS
         */
        function __construct($name, $base, $devel)
        /*
         * FUNCTION
         *      constructor
         ****
         */
        {
            $this->lima_app     = $name;
            $this->lima_base    = $base;
            $this->lima_devel   = $devel;
        }
    
        /****m* get_config/loads
         * SYNOPSIS
         */
        function load($app, $path = NULL, $name = 'config')
        /*
         * FUNCTION
         *      loads yaml configuration file(s)
         * INPUTS
         *      * $app (string) -- name of application to load configuration files for
         ****
         */
        {
            $name = basename($name, '.yml');
        
            if (is_null($path)) {
                $base = $this->lima_base;
                $path = $base . '/etc/' . $this->lima_app;
            } else {
                $base = realpath($path . '/../../..');
            }
        
            $local = false;

            // load global config file
            $cfg = array();
        
            if (file_exists($path . '/' . $name . '.yml')) {
                if ($cfg =& parent::load($path . '/' . $name . '.yml')) {
                    $cfg = lima_type_array::flatten($cfg, true);
                }
            }

            // load local config file and merge both configs
            if (file_exists($path . '/' . $name . '_local.yml')) {
                if ($local =& parent::load($path . '/' . $name . '_local.yml')) {
                    $cfg = array_merge($cfg, lima_type_array::flatten($local, true));
                }
            }
        
            // flatten recursive config array - this process should be cached in either way
        
            $cfg['common.application.development'] = $this->lima_devel;
            $cfg['common.application.path'] = $base;

            return $cfg;
        }
    }
}
