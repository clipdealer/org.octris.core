<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core {
    use \org\octris\core\app as app;
    use \org\octris\core\validate as validate;
    use \org\octris\core\provider as provider;

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
         * @octdoc  p:config/$module
         * @var     string
         */
        protected $module = '';
        /**/

        /**
         * Name of configuration file.
         *
         * @octdoc  p:config/$name
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
         * Sets defaults for configuration. Values are only set, if the keys of the values are not already available
         * in the configuration.
         *
         * @octdoc  m:collection/setDefaults
         * @param   mixed       $value      Value(s) to set as default(s).
         */
        public function setDefaults($value)
        /**/
        {
            if (($tmp = self::normalize($value, true)) === false) {
                throw new Exception('don\'t know how to handle parameter of type "' . gettype($array) . '"');
            } else {
                $data = $this->getArrayCopy();
                $data = array_merge($tmp, $data);

                $this->exchangeArray($data);
            }
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
            return new \org\octris\core\config\filter($this, $prefix);
        }

        /**
         * Save configuration file to destination. if destination is not
         * specified, try to save in ~/.octris/<module>/<name>.yml.
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

            return file_put_contents($file, yaml_emit((array)\org\octris\core\type\collection\deflatten($this)));
        }

        /**
         * Load configuration file. the loader looks in the following places,
         * loads the configuration file and merges them in specified lookup order:
         *
         * - T_PATH_ETC/config.yml
         * - T_PATH_ETC/config_local.yml
         * - ~/.octris/config.yml
         *
         * whereat the confíguration file name -- in this example 'config' -- may be overwritten by the first parameter.
         * The constant T_ETC_PATH is resolved by the value of the second parameter. By default T_ETC_PATH is resolved to
         * the 'etc' path of the current running application.
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
            $module = ($module == ''
                        ? provider::access('env')->getValue('OCTRIS_APP', validate::T_PROJECT)
                        : $module);
            $cfg = array();

            // load default module config file
            $path = app::getPath(app::T_PATH_ETC, $module);
            $file = $path . '/' . $name . '.yml';

            if (is_readable($file) && ($tmp = yaml_parse_file($file)) && !is_null($tmp)) {
                $cfg = array_merge($cfg, \org\octris\core\type\collection::flatten($tmp));
            }

            // load local config file
            $file = $path . '/' . $name . '_local.yml';

            if (is_readable($file) && ($tmp = yaml_parse_file($file)) && !is_null($tmp)) {
                $cfg = array_merge($cfg, \org\octris\core\type\collection::flatten($tmp));
            }

            // load global framework configuration
            $info = posix_getpwuid(posix_getuid());
            $file = $info['dir'] . '/.octris/' . $module . '/' . $name . '.yml';

            if (is_readable($file) && ($tmp = yaml_parse_file($file)) && !is_null($tmp)) {
                $cfg = array_merge($cfg, \org\octris\core\type\collection::flatten($tmp));
            }

            return new \org\octris\core\type\collection($cfg);
        }
    }
}
