<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\auth {
    /**
     * Class for storing authenticated identity.
     *
     * @octdoc      c:auth/identity
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class identity
    /**/
    {
        /**
         * Authentication status code.
         *
         * @octdoc  v:identity/$code
         * @var     int
         */
        protected $code;
        /**/

        /**
         * Properties stored in the identity.
         *
         * @octdoc  v:identity/$identity
         * @var     array
         */
        protected $identity = array();
        /**/

        /**
         * Internal ID of identity. This ID should be always the same, for the same identity
         * parameters.
         *
         * @octdoc  v:identiy/$id
         * @var     string
         */
        protected $id;
        /**/

        /**
         * Construct.
         *
         * @octdoc  m:identity/__construct
         * @param   int             $code                   Status code.
         * @param   array           $identity               Settings, that are stored in the identity.
         */
        public function __construct($code, array $identity)
        /**/
        {
            $this->code     = $code;
            $this->identity = $identity;

            $this->id = hash('sha256', serialize($identity));
        }

        /**
         * Returns true, if identity is valid.
         *
         * @octdoc  m:identity/isValid
         * @param   bool                                    Identity validation status.
         */
        public function isValid()
        /**/
        {
            return ($this->code === \org\octris\core\auth::T_AUTH_SUCCESS);
        }

        /**
         * Return hash Id of identity.
         *
         * @octdoc  m:identity/getId
         * @param   string                                  Hash Id of identity.
         */
        public function getId()
        /**/
        {
            return $this->id;
        }

        /**
         * Return status code of identity authentication.
         *
         * @octdoc  m:identity/getCode
         * @param   int                                     Status code.
         */
        public function getCode()
        /**/
        {
            return $this->code;
        }

        /**
         * Returns the stored identity data.
         *
         * @octdoc  m:identity/getIdentity
         * @param   array                                   Identity data.
         */
        public function getIdentity()
        /**/
        {
            return $this->identity;
        }
    }
}
