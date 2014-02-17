<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\app\cli {
    /**
     * Provides readline functionality either by using built-in readline
     * capabilities or by an emulation, if built-in functionality is not
     * available.
     *
     * @octdoc      c:cli/readline
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     *
     * @depends     \org\octris\core\app\cli\readline\bash
     * @depends     \org\octris\core\app\cli\readline\emulated
     * @depends     \org\octris\core\app\cli\readline\native
     * @depends     \org\octris\core\app\cli\readline_if
     */
    class readline
    /**/
    {
        /**
         * Size of history, maximum entries.
         *
         * @octdoc  d:readline/T_HISTORY_SIZE
         */
        const T_HISTORY_SIZE = 100;
        /**/

        /**
         * Class to use for new instance.
         *
         * @octdoc  p:readline/$class
         * @type    \org\octris\core\app\cli\readline
         */
        protected static $class = null;
        /**/

        /**
         * Instances of readline. Same history files share the sime readline instance.
         *
         * @octdoc  p:readline/$instances
         * @type    array
         */
        protected static $instances = array();
        /**/

        /**
         * Registered readline devices.
         *
         * @octdoc  p:readline/$devices
         * @type    array
         */
        protected static $devices = array();
        /**/

        /**
         * Register a readline device.
         *
         * @octdoc  m:readline/registerDevice
         * @param   string          $class                  Full qualified classname of the device.
         * @param   int             $priority               (Optional) priority for device.
         */
        public static function registerDevice($class, $priority = 0)
        /**/
        {
            self::$devices[$class] = $priority;
        }

        /**
         * Returns a new instance of readline. Note that no history functionality is available, if no
         * history path is provided.
         *
         * @octdoc  m:readline/getInstance
         * @param   string          $history_file           Optional path to a history file.
         * @return  \org\octris\core\app\cli\readline       Instance of readline.
         */
        public static function getInstance($history_file = '')
        /**/
        {
            if (!isset(self::$instances[$history_file])) {
                if (is_null(self::$class)) {
                    // detect and decide wich readline device to use
                    arsort(self::$devices);

                    foreach (self::$devices as $device => $priority) {
                        if (in_array('org\octris\core\app\cli\readline_if', class_implements($device)) 
                            && $device::detect()) {
                            self::$class = $device;
                            break;
                        }
                    }
                }

                self::$instances[$history_file] = new self::$class($history_file);
            }

            return self::$instances[$history_file];
        }

        /** no need to ever create an instance of this class **/
        protected function __construct() {}
        protected function __clone() {}
        /**/
    }

    readline::registerDevice('\org\octris\core\app\cli\readline\native', -1);
    readline::registerDevice('\org\octris\core\app\cli\readline\bash', -2);
    readline::registerDevice('\org\octris\core\app\cli\readline\emulated', -3);
}
