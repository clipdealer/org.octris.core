<?php

namespace org\octris\core {
    /**
     * Database objects (DBO) attempts to provide an object abstraction of database data.
     *
     * @octdoc      c:core/dbo
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    abstract class dbo
    /**/
    {
        /**
         * Master connection type.
         * 
         * @octdoc  d:dbo/T_CN_MASTER
         */
        const T_CN_MASTER = 'master';
        /**/
        
        /**
         * Slave connection type.
         * 
         * @octdoc  d:dbo/T_CN_SLAVE
         */
        const T_CN_SLAVE = 'slaves';
        /**/
    }
}
