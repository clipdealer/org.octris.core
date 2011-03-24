<?php

namespace org\octris\core\dbo\mongodb {
    /**
     * Handles mongodb connections for connection pool.
     *
     * @octdoc      c:mongodb/pool
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class pool extends \org\octris\core\dbo\pool
    /**/
    {
        /**
         * Open a new connection to the database. This method needs to be implemented by database specific pool implementation.
         *
         * @octdoc  m:pool/getConnection
         * @param   string          $type                   Connection type to return a connection for.
         * @param   array           $params                 Connection parameters for database connection.
         * @return  object                                  Instance of database connection.
         */
        protected function getConnection($type, array $params)
        /**/
        {
            return new \org\octris\core\dbo\mongodb\connection(
                $type, $this,
                $params[$type],
                $params['username'],
                $params['password'],
                $params['database'],
                $params['port']
            );
        }
    }
}
