<?php

namespace org\octris\core {
    use \org\octris\core\app as app;
    use \org\octris\core\type\collection as collection;
    
    /**
     * class: core/config
     *
     * handles application configuration
     *
     * @copyright   (c) 2010 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     **
     */

    class config extends \org\octris\core\type\collection {
        /**
         * property: config/$module
         *
         * name of module the configuration belongs to
         */
        protected $module = '';
        /**/
        
        /**
         * property: config/$name
         *
         * name of configuration file
         */
        protected $name = '';
        /**/
        
        /**
         * method: config/__construct
         *
         * constructor
         *
         * @param   string  $module     name of module configuration belongs to
         * @param   string  $name       name of configuration file
         */
        public function __construct($module, $name) 
        /**/
        {
            $this->module = $module;
            $this->name   = $name;
            
            $data = self::load($name, $module);
            
            parent::__construct($data);
        }

        /**
         * method: config/filter
         *
         * filter configuration for prefix
         *
         * @param   string      $prefix     prefix to use for filter
         * @return  Iterator                filter iterator
         */
        public function filter($prefix)
        /**/
        {
            return new \org\octris\core\config\filter($this->getIterator(), $prefix);
        }

        /**
         * method: config/save
         *
         * save configuration file to destination. if destination is not
         * specified, try to save in ~/config/<module>/<name>.yml
         *
         * @param   string  $file       otional destination to save configuration to
         */
        public function save($file = '')
        /**/
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

        /**
         * method: config/_load
         *
         * load configuration file. the loader looks in the following places, 
         * loads the configuration file and merges them in specified lookup order:
         *
         * - T_PATH_ETC/config.yml
         * - T_PATH_ETC/config_local.yml
         * - ~/.octris/config.yml
         *
         * @param   string      $name   optional name of configuration file to load
         * @param   string      $module optional name of module to laod
         * @return  collection  contents of the configuration file
         */
        private static function load($name = 'config', $module = '')
        /**/
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
