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
         * @octdoc  d:dbo/T_DBO_UPDATE
         */
        const T_DBO_UPDATE = 'master';
        /**/
        
        /**
         * Slave connection type.
         * 
         * @octdoc  d:dbo/T_DBO_SELECT
         */
        const T_DBO_SELECT = 'slaves';
        /**/
    }
}
