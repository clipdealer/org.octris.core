<?php

namespace org\octris\core {
    use \org\octris\core\app as app;
    use \org\octris\core\type\collection as collection;
    
    /**
     * handles application configuration
     *
     * @octdoc      c:core/config
     * @copyright   (c) 2010-2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class config extends \org\octris\core\type\collection 
    /**/
    {
        /**
         * Name of module the configuration belongs to.
         *
         * @octdoc  v:config/$module
         * @var     string
         */
        protected $module = '';
        /**/
        
        /**
         * Name of configuration file.
         *
         * @octdoc  v:config/$name
         * @var     string
         */
        protected $name = '';
        /**/
        
        /**
         * Constructor.
         *
         * @octdoc  m:config/__construct
         * @param   string  $module     Name of module configuration belongs to.
         * @param   string  $name       Name of configuration file.
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
         * Filter configuration for prefix.
         *
         * @octdoc  m:config/filter
         * @param   string                              $prefix     Prefix to use for filter.
         * @return  \org\octris\core\config\filter                  Filter iterator.
         */
        public function filter($prefix)
        /**/
        {
            return new \org\octris\core\config\filter($this->getIterator(), $prefix);
        }

        /**
         * Save configuration file to destination. if destination is not
         * specified, try to save in ~/config/<module>/<name>.yml.
         *
         * @octdoc  m:config/save
         * @param   string  $file       Optional destination to save configuration to.
         * @return  bool                Returns TRUE on success, otherwise FALSE.
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
            
            return file_put_contents($file, yaml_emit(
                $this->deflatten()->getArrayCopy()
            ));
        }

        /**
         * Load configuration file. the loader looks in the following places, 
         * loads the configuration file and merges them in specified lookup order:
         *
         * - T_PATH_ETC/config.yml
         * - T_PATH_ETC/config_local.yml
         * - ~/.octris/config.yml
         *
         * @octdoc  m:config/_load
         * @param   string                              $name       Optional name of configuration file to load.
         * @param   string                              $module     Optional name of module to laod.
         * @return  \org\octris\core\type\collection                Contents of the configuration file.
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
