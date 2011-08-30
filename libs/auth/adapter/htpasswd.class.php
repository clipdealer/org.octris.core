<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\auth\adapter {
    /**
     * Allows authentication against a htpasswd file.
     *
     * @octdoc      c:adapter/htpasswd
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class htpasswd implements \org\octris\core\auth\adapter_if
    /**/
    {
        /**
         * Username to authenticate with adapter.
         *
         * @octdoc  v:htpasswd/$username
         * @var     string
         */
        protected $username = '';
        /**/

        /**
         * Credential to authenticate with adapter.
         *
         * @octdoc  v:htpasswd/$credential
         * @var     string
         */
        protected $credential = '';
        /**/

        /**
         * Htpasswd file to use for authentication.
         *
         * @octdoc  v:htpasswd/$file
         * @var     string
         */
        protected $file;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:htpasswd/__construct
         * @param   string          $file               Htpasswd file to use for authentication.
         */
        public function __construct($file)
        /**/
        {
            if (!is_file($file) || !is_readable($file)) {
                throw new \Exception(sprintf('File not found or file is not readable "%s"', $file));
            }

            $this->file = $file;
        }

        /**
         * Set's a username to be authenticated.
         *
         * @octdoc  m:htpasswd/setUsername
         * @param   string          $username           Username to authenticate.
         */
        public function setUsername($username)
        /**/
        {
            $this->username = $username;
        }

        /**
         * Set's a credential to be authenticated.
         *
         * @octdoc  m:htpasswd/setCredential
         * @param   string          $credential         Credential to authenticate.
         */
        public function setCredential($credential)
        /**/
        {
            $this->credential = crypt($credential);
        }

        /**
         * Authenticate.
         *
         * @octdoc  m:htpasswd/authenticate
         * @return  \org\octris\core\auth\identity                  Instance of identity class.
         */
        public function authenticate()
        /**/
        {
            $result = \org\octris\core\auth::T_IDENTITY_FAILURE;

            if (empty($this->username)) {
                throw new \Exception('Username cannot be empty');
            }
            if (empty($this->credential)) {
                throw new \Exception('Credential cannot be empty');
            }

            if (!($fp = fopen($this->file, 'r'))) {
                throw new \Exception(sprintf('Unable to read file "%s"', $this->file));
            } else {
                $result = \org\octris\core\auth::T_IDENTITY_UNKNOWN;

                while (!feof($fp)) {
                    if (list($username, $password) = fgetcsv($fp, 512, ':') && $username == $this->username) {
                        if ($result != \org\octris\core\auth::T_IDENTITY_UNKNOWN) {
                            $result = \org\octris\core\auth::T_IDENTITY_AMBIGUOUS;
                            break;
                        } elseif ($this->credential == $password) {
                            $result = \org\octris\core\auth::T_AUTH_SUCCESS;
                            break;
                        } else {
                            $result = \org\octris\core\auth::T_CREDENTIAL_INVALID;
                        }
                    }
                }

                fclose($fp);
            }

            return new \org\octris\core\auth\identity(
                $result,
                array(
                    'username'   => $this->username,
                    'credential' => $this->credential
                )
            );
        }
    }
}
