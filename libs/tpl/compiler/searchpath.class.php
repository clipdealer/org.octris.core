<?php

namespace org\octris\core\tpl\compiler {
    /****c* compiler/searchpath
     * NAME
     *      searchpath
     * FUNCTION
     *      Manage searchpathes for compiler. This is a static class.
     * COPYRIGHT
     *      copyright (c) 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */

    class searchpath {
        /****v* searchpath/$registry
         * SYNOPSIS
         */
        protected static $registry = array();
        /*
         * FUNCTION
         *      registry for pathes
         ****
         */
        
        /** prevent creation of instance **/
        protected function __construct() {}
        protected function __clone() {}
        
        /****m* searchpath/addPath
         * SYNOPSIS
         */
        public static function addPath($path)
        /*
         * FUNCTION
         *      add one or multiple search path to registry
         * INPUTS
         *      * $path (mixed) -- array or string of path to add to registry
         ****
         */
        {
            if (!is_array($path)) $path = array($path);
            
            array_unique(array_merge(self::$registry, $path));
        }
        
        /****m* searchpath/removePath
         * SYNOPSIS
         */
        public static function removePath($path)
        /*
         * FUNCTION
         *      remove path from registry
         * INPUTS
         *      * $path (string) -- path to remove
         ****
         */
        {
            if (($idx = array_search($path, self::$registry)) !== false) {
                unset($path[$idx]);
            }
        }
        
        /****m* searchpath/getPath
         * SYNOPSIS
         */
        public function getPath()
        /*
         * FUNCTION
         *      return searchpath
         * OUTPUTS
         *      (array) -- search path registry
         ****
         */
        {
            return self::$registry;
        }
        
        /****m* searchpath/findFile
         * SYNOPSIS
         */
        public static function findFile($filename)
        /*
         * FUNCTION
         *      lookup a file in the searchpath 
         * INPUTS
         *      * $filename (string) -- name of file to lookup
         * OUTPUTS
         *      (mixed) -- returns full path of file or false, if file could not be located
         ****
         */
        {
            $return = false;
            
            foreach (self::$registry as $path) {
                if (file_exists($path . '/' . $filename)) {
                    $return = $path . '/' . $filename;
                    break;
                }
            }
            
            return $return;
        }
    }
}
