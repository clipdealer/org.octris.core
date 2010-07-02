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
        
        /****v* device/$parse_mode
         * SYNOPSIS
         */
        protected $parse_mode = '/^(?P<access>(r|w|a))(?P<binary>b?)(?P<rw>\+?)$/i';
        /*
         * FUNCTION
         *      regular expression for parsing filemode
         ****
         */

        /****v* device/$mode
         * SYNOPSIS
         */
        protected $mode = 0;
        /*
         * FUNCTION
         *      access mode
         ****
         */

        /****d* device/T_READ, T_WRITE, T_APPEND, T_BINARY
         * SYNOPSIS
         */
        const T_READ   = 1;
        const T_WRITE  = 2;
        const T_APPEND = 4;
        const T_BINARY = 8;
        /*
         * FUNCTION
         *      access modes to form ~mode~ bit-field
         ****
         */
        
        /****m* device/parseMode
         * SYNOPSIS
         */
        protected function parseMode($mode)
        /*
         * FUNCTION
         *      parse mode and set bit-field according to mode parameters
         * INPUTS
         *      * $mode (string) -- mode to parse
         * OUTPUTS
         *      (bool) -- returns true for valid mode, otherwise false
         ****
         */
        {
            if (!preg_match($this->parse_mode, $mode, $match)) {
                trigger_error('unable to parse access mode "' . $mode . '"');
                return false;
            }
            
            switch ($match['access']) {
            case 'r':
                $this->mode = self::T_READ;
                if ($match['rw']) $this->mode = $this->mode | self::T_WRITE;
                break;
            case 'w':
                $this->mode = self::T_WRITE;
                if ($match['rw']) $this->mode = $this->mode | self::T_READ;
                break;
            case 'a':
                $this->mode = (self::T_APPEND | self::T_READ);
                if ($match['rw']) $this->mode = $this->mode | self::T_WRITE;
                break;
            }
            
            if ($match['binary']) $this->mode = $this->mode | self::T_BINARY;
            
            return true;
        }
        
        /****m* device/register
         * SYNOPSIS
         */
        public static function register($name, array $options = array())
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
         *      * $options (string) -- (optional)
         ****
         */
        {
            if (get_class() == ($class = get_called_class())) {
                throw new \Exception("invalid context!");
            } elseif (isset(self::$registry[$name])) {
                throw new \Exception('device already registered!');
            }

            self::$registry[$name] = $options;
            
            stream_wrapper_register($name, $class);
        }
    }
}
