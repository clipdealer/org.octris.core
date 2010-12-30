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

    class config extends \org\octris\core\type\collection {
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
        public function __construct($module, $name) 
        /*
         * FUNCTION
         *      constructor
         * INPUTS
         *      * $module (string) -- name of module configuration belongs to
         *      * $name (string) -- name of configuration file
         ****
         */
        {
            $this->module = $module;
            $this->name   = $name;
            
            $data = self::load($name, $module);
            
            parent::__construct($data);
        }

        /****m* config/filter
         * SYNOPSIS
         */
        public function filter($prefix)
        /*
         * FUNCTION
         *      filter configuration for prefix
         * INPUTS
         *      * $prefix (string) -- prefix to use for filter
         * OUTPUTS
         *      (Iterator) -- filter iterator
         ****
         */
        {
            return new \org\octris\core\config\filter($this->getIterator(), $prefix);
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
            
            file_put_contents($file, yaml_emit(
                $this->deflatten()->getArrayCopy()
            ));
        }

        /****m* config/_load
         * SYNOPSIS
         */
        private static function load($name = 'config', $module = '')
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
         *      *   $name (string) -- (optional) name of configuration file to load
         *      *   $module (string) -- (optional) name of module to laod configuration for
         * OUTPUTS
         *      (collection) -- collection representation of the loaded
         *      configuration file
         *      (bool) -- false, if a configuration could not be loaded
         ****
         */
        {
            // initialization
            $module = ($module == '' ? $_ENV['OCTRIS_APP']->value : $module);
            $cfg    = new collection();

            // load default module config file
            $path = app::getPath(app::T_PATH_ETC, $module);
            $file = $path . '/' . $name . '.yml';
            
            if (is_readable($file) && ($tmp = yaml_parse_file($file)) && !is_null($tmp)) {
                $tmp = new collection($tmp);
                $cfg->merge($tmp->flatten());
            }

            // load local config file
            $file = $path . '/' . $name . '_local.yml';
            
            if (is_readable($file) && ($tmp = yaml_parse_file($file)) && !is_null($tmp)) {
                $tmp = new collection($tmp);
                $cfg->merge($tmp->flatten());
            }
        
            // load global framework configuration
            $info = posix_getpwuid(posix_getuid());
            $file = $info['dir'] . '/.octris/' . $module . '/' . $name . '.yml';
            
            if (is_readable($file) && ($tmp = yaml_parse_file($file)) && !is_null($tmp)) {
                $tmp = new collection($tmp);
                $cfg->merge($tmp->flatten());
            }

            return $cfg;
        }
    }
}
