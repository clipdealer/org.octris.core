<?php

namespace org\octris\org\fs\file
    /****c* file/yaml
     * NAME
     *      yaml
     * FUNCTION
     *      yaml loading by either using SPYC or php yaml loader
     * COPYRIGHT
     *      copyright (c) 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */
 
    if (!extension_loaded('syck')) {
        // fallback to spyc, if syck extension is not loaded (SuSE sucks!)
        require_once('base/libs/3rdparty/spyc/spyc.php');
    
        function syck_load($filename) {
            return Spyc::YAMLLoad($filename);
        }
    }
 
    class yaml {
        /****m* yaml/__construct
         * SYNOPSIS
         */
        function __construct() 
        /*
         * FUNCTION
         *      constructor
         ****
         */
        {
        }

        /****m* yaml/load
         * SYNOPSIS
         */
        function load($filename) 
        /*
         * FUNCTION
         *
         ****
         */
        {
            $return = false;
        
            if (file_exists($filename)) {
                $return = syck_load(file_get_contents($filename));
            }
        
            return $return;
        }
    
        /****m* yaml/save
         * SYNOPSIS
         */
        function save($filename = '', $cfg) 
        /*
         * FUNCTION
         *      saves yaml to 
         ****
         */
        {
            die("not implemented yet!");
        }
    }
}
