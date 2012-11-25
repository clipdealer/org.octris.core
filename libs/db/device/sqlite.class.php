<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\db\device {
    /**
     * SQLite database device. 
     * 
     * @octdoc      c:device/sqlite
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     * 
     * @todo        Support encryption Libraries:
     *              * http://sqlcipher.net/
     *              * http://www.hwaci.com/sw/sqlite/see.html
     *              * http://sqlite-crypt.com/index.htm
     */
    class sqlite extends \org\octris\core\db\device
    /**/
    {
        /**
         * SQLite flags of how to open database.
         *
         * @octdoc  d:sqlite/T_READONLY, T_READWRITE, T_CREATE
         * @var     int
         */
        const T_READONLY  = SQLITE3_OPEN_READONLY;
        const T_READWRITE = SQLITE3_OPEN_READWRITE;
        const T_CREATE    = SQLITE3_OPEN_CREATE;
        /**/

        /**
         * Path to the SQLite database, or :memory: to use in-memory database.
         *
         * @octdoc  p:sqlite/$file
         * @var     string
         */
        protected $file;
        /**/
        
        /**
         * flags of how to open SQLite database.
         *
         * @octdoc  p:sqlite/$flags
         * @var     int
         */
        protected $flags;
        /**/

        /**
         * Key to use for encrypted databases. Note, that database encryption is only supported, when
         * 
         *
         * @octdoc  p:sqlite/$key
         * @var     string
         */
        protected $key;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:sqlite/__construct
         * @param   string          $file               Path to the SQLite database, or :memory: to use in-memory database.
         * @param   int             $flags              Optional flags of how to open SQLite database.
         * @param   string          $key                Optional key when database encryption is used.
         */
        public __construct($file, $flags = null, $key = null)
        /**/
        {
            $this->file  = $file;
            $this->flags = self::T_READWRITE | self::T_CREATE;
            $this->key   = $key;
        }

        /**
         * Create database connection.
         *
         * @octdoc  m:sqlite/getConnection
         * @return  \org\octris\core\db\device\sqlite\connection            Connection to a sqlite database.
         */
        public getConnection()
        /**/
        {
            $cn = new \org\octris\core\db\device\sqlite\connection(
                array(
                    'file'  => $this->host,
                    'flags' => $this->port,
                    'key'   => $this->database
                )
            );

            return $cn;
        }
    }
}
