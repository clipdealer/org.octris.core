<?php

namespace org\octris\core
    use \org\octris\core\config as config;
    use \org\octris\core\cache\proxy as proxy;

    /****c* core/settings
     * NAME
     *      settings
     * FUNCTION
     *      user specific application settings handling
     * COPYRIGHT
     *      copyright (c) 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */

    class settings {
        /****v* settings/$data
         * SYNOPSIS
         */
        protected static $data = array();
        /*
         * FUNCTION
         *      settings data
         ****
         */
        
        /****m* settings/init
         * SYNOPSIS
         */
        protected static function init()
        /*
         * FUNCTION
         *      initialize settings object
         * TODO
         *      *   load default settings from config-file
         ****
         */
        {
            $name = config::get('common.settings.cookie');
            
            $_COOKIE->validate($name, config::T_PRINT);

            if (is_array(($data = json_decode($_COOKIE[$name], true)))) {
                $data = array();
            }

            self::$settings = new \org\octris\core\validate\wrapper($data);
        }
        
        /****m* settings/__construct, __clone
         * SYNOPSIS
         */
        private function __construct() {}
        private function __clone() {}
        /*
         * FUNCTION
         *      keep class static
         ****
         */

        /****m* settings/setCookie
         * SYNOPSIS
         */
        public static function setCookie()
        /*
         * FUNCTION
         *      serialize a merged array of defaults (if loaded) and user specific
         *      settings and store them in the settings cookie.
         ****
         */
        {
            $data = json_encode(self::$data->getArrayCopy());

            setcookie(
                config::get('common.settings.cookie'),
                $data,
                config::get('common.settings.lifetime'),
                '/'
            );
        }

        /****m* settings/set
         * SYNOPSIS
         */
        public static function set($name, $value)
        /*
         * FUNCTION
         *      store a value for a user specific setting
         * INPUTS
         *      * $name (string) -- name of property to set
         *      * $value (mixed) -- value to set for specified property
         ****
         */
        {
            self::$data[$name] = $value;
        }

        /****m* settings/get
         * SYNOPSIS
         */
        public static function get($name, $type)
        /*
         * FUNCTION
         *      return value of spacified setting
         * INPUTS
         *      * $name (string) -- name of setting to return
         *      * $type (string) -- expected type for validation
         * OUTPUTS
         *      (mixed) -- returns setting for specified name
         ****
         */
        {
            if (self::$data->validate($name, $type)) {
                $value = self::$data[$name]->value;
            } else {
                $value = '';
            }

            return $value;
        }
    }
}
