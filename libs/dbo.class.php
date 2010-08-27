<?php

namespace org\octris\core {
    /****c* core/dbo
     * NAME
     *      dbo
     * FUNCTION
     *      database objects (DBO) attempts to provide an object abstraction of database data
     * COPYRIGHT
     *      copyright (c) 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */

    abstract class dbo {
        /****v* dbo/$dbal
         * SYNOPSIS
         */
        protected $dbal;
        /*
         * FUNCTION
         *      instance of database abstraction layer (dbal)
         ****
         */
     
        /****m* dbo/__construct
         * SYNOPSIS
         */
        function __construct()
        /*
         * FUNCTION
         *      constructor -- to be overwritten by a sub-class
         ****
         */
        {
            $this->dbal = lima_dbal::getInstance();
        }
 
        /****m* dbo/getConnection
         * SYNOPSIS
         */
        function getConnection($type)
        /*
         * FUNCTION
         *      returns connection to a database
         * INPUTS
         *      * $type (int) -- type of connection to return
         * OUTPUTS
         *      (connection) -- a dbal connection
         ****
         */
        {
            return $this->dbal->getConnection($type);
        }
    }
}
