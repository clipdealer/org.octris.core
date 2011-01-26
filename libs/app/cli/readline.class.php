<?php

namespace org\octris\core\app\cli {
    /**
     * Provides readline functionality either by using built-in readline
     * capabilities or by an emulation, if built-in functionality is not
     * available.
     *
     * @octdoc      c:cli/readline
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    abstract class readline
    /**/
    {
        /**
         * Class to use for new instance.
         *
         * @octdoc  v:readline/$class
         * @var     \org\octris\core\app\cli\readline
         */
        protected static $class = null;
        /**/
        
        /**
         * Get method.
         *
         * @octdoc  m:readline/get
         * @abstract
         */
        abstract public function get($prompt = '', $default = '', $force = false);
        /**/
        
        /**
         * Returns a new instance of readline.
         *
         * @octdoc  m:readline/getInstance
         * @return  \org\octris\core\app\cli\readlin        Instance of readline.
         */
        public final static function getInstance()
        /**/
        {
            if (is_null(self::$class)) {
                // detect and decide whether to use native or emulated readline
                if (function_exists('readline')) {
                    self::$class = '\org\octris\core\app\cli\readline\native';
                } else {
                    self::$class = '\org\octris\core\app\cli\readline\emulated';
                }
            }
            
            $class = self::$class;
            
            return new $class();
        }
    }
}
