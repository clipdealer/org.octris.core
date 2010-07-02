<?php

namespace org\octris\core\fs {
    /****c* fs/device
     * NAME
     *      device
     * FUNCTION
     *      device base class
     * COPYRIGHT
     *      copyright (c) 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */

    abstract class device {
        /****v* device/$registry
         * SYNOPSIS
         */
        protected static $registry = array();
        /*
         * FUNCTION
         *      registry for devices
         ****
         */
        
        /****m* device/register
         * SYNOPSIS
         */
        public static function register($name, $device, array $options = array())
        /*
         * FUNCTION
         *      Register a device as stream wrapper. The method requires a name as first parameter. The device will
         *      be registered with the specified name and it's possible to access the stream wrapper filesystem by
         *      specifieng a file (URL) to PHP native functions (eg.: fopen) using the name as protocol name.
         * EXAMPLE
         *      ..  source: php
         *          \org\octris\core\device\gridfs::register('gridfs', array('host' => '...', ...));
         *          fopen('gridfs://...');
         * INPUTS
         *      * $name (string) -- name of device, protocol name to register device with
         *      * $device (string) -- 
         *      * $options (string) -- (optional)
         ****
         */
        {
            if (get_class() == ($class = get_called_class())) {
                throw new Exception("invalid context!");
            } elseif (isset(self::$registry[$name])) {
                throw new Exception('device already registered!');
            }

            self::$registry[$name] = $options;
            
            stream_wrapper_register($name, $class);
        }
    }
}
