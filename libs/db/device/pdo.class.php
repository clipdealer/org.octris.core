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
     * PDO database device.
     *
     * @octdoc      c:device/pdo
     * @copyright   copyright (c) 2014 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class pdo extends \org\octris\core\db\device
    /**/
    {
        /**
         * Data Source Name (DSN).
         *
         * @octdoc  p:pdo/$dsn
         * @type    string
         */
        protected $dsn;
        /**/
        
        /**
         * Username to use for connection.
         *
         * @octdoc  p:pdo/$username
         * @type    string
         */
        protected $username;
        /**/
        
        /**
         * Password to use for connection.
         *
         * @octdoc  p:pdo/$password
         * @type    string
         */
        protected $password;
        /**/

        /**
         * Additional options for connection.
         *
         * @octdoc  p:pdo/$options
         * @type    array
         */
        protected $options;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:pdo/__construct
         * @param   string          $dsn                Data Source Name (DSN).
         * @param   string          $username           Optional username to use for connection.
         * @param   string          $password           Optional password to use for connection.
         * @param   array           $options            Optional additional options.
         */
        public function __construct($dsn, $username = null, $password = null, array $options = array())
        /**/
        {
            parent::__construct();

            $this->addHost(\org\octris\core\db::T_DB_MASTER, array(
                'dsn'      => ($this->dsn      = $dsn),
                'username' => ($this->username = $username),
                'password' => ($this->password = $password),
                'options'  => ($this->options  = $options)
            ));
        }

        /**
         * Create database connection.
         *
         * @octdoc  m:pdo/createConnection
         * @param   array                       $options                Host configuration options.
         * @return  \org\octris\core\db\device\pdo\connection           Connection to a pdo database.
         */
        protected function createConnection(array $options)
        /**/
        {
            $cn = new \org\octris\core\db\device\pdo\connection($this, $options);

            return $cn;
        }
    }
}
