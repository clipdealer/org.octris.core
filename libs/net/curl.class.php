<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\net {
    /**
     * Generic cURL class.
     * 
     * @octdoc      c:net/curl
     * @copyright   Copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    abstract class curl
    /**/
    {
        /**
         * Supported schemes. Is empty, if there is no limitation for 
         * protocols.
         *
         * @octdoc  p:curl/$schemes
         * @var     array
         */
        protected static $schemes = array();
        /**/

        /**
         * Options for curl client.
         *
         * @octdoc  p:curl/$options
         * @var     array
         */
        protected $options = array();
        /**/

        /**
         * Session assigned to the client.
         *
         * @octdoc  p:curl/$session
         * @var     \org\octris\core\net|null
         */
        protected $session = null;
        /**/

        /**
         * Event listener.
         * 
         * @octdoc  p:curl/$listener
         * @var     callable|null
         */
        protected $listener = null;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:curl/__construct
         * @param   \org\octris\core\type\uri       $uri            URI 
         */
        public function __construct(\org\octris\core\type\uri $uri)
        /**/
        {
            if (!extension_loaded('curl')) {
                throw new \Exception('Missing ext/curl');
            }

            if (count(static::$schemes) > 0 && !in_array($uri->scheme, static::$schemes)) {
                throw new \Exception(sprintf(
                    'Invalid URI specified, supported protocols are "%s"',
                    implode(', ', static::$schemes)
                ));
            }

            $this->options[CURLOPT_URL]            = (string)$uri;
            $this->options[CURLOPT_RETURNTRANSFER] = true;
        }

        /**
         * Clone.
         *
         * @octdoc  m:curl/__clone
         */
        public function __clone()
        /**/
        {
            // cloned client instance is not part of a session
            $this->session = null;
        }

        /**
         * Set timeout in seconds or microseconds (as float).
         *
         * @octdoc  m:net/setTimeout
         * @param   $timout             $timeout            The timeout to set.
         */
        public function setTimeout($timeout)
        /**/
        {
            if (is_float($sec)) {
                unset($this->options[CURLOPT_CONNECTTIMEOUT]);
                $this->options[CURLOPT_CONNECTTIMEOUT_MS] = (int)($sec * 1000);
            } else {
                unset($this->options[CURLOPT_CONNECTTIMEOUT_MS]);
                $this->options[CURLOPT_CONNECTTIMEOUT] = $sec;
            }
        }

        /**
         * Return options set for client.
         *
         * @octdoc  m:net/getOptions
         * @return  array                                   Curl options.
         */
        public function getOptions()
        /**/
        {
            return $this->options;
        }

        /**
         * Add curl client to a session.
         *
         * @octdoc  m:curl/setSession
         * @param   \org\octris\core\net        $sesstion   Session to assign to the client.
         */
        public function setSession(\org\octris\core\net $session)
        /**/
        {
            if (!is_null($this->session)) {
                throw new \Exception('Client is already assigned to a session');
            }

            $this->session = $session;
        }

        /**
         * Get session the client is assigned to.
         *
         * @octdoc  m:curl/getSession
         * @return  \org\octris\core\net                    Session the client is assigned to.
         */
        public function getSession()
        /**/
        {
            return $this->session;
        }

        /**
         * Set event listener.
         *
         * @octdoc  m:curl/setListener
         * @param   callable        $listener               Listener to set.
         */
        public function setListener(callable $listener)
        /**/
        {
            $this->listener = $listener;
        }

        /**
         * Get event listener.
         *
         * @octdoc  m:curl/getListener
         * @return  callable                                Listener set.
         */
        public function getListener()
        /**/
        {
            return $this->listener;
        }

        /**
         * Execute client.
         *
         * @octdoc  m:curl/execute
         */
        public function execute()
        /**/
        {
            if (!is_null($this->session)) {
                throw new \Exception('Unable to execute a client that is assigned to a session');
            }
    
            $ch = curl_init();
            curl_setopt_array($ch, $this->options);

            $return = curl_exec($ch);

            curl_close($ch);

            if (!is_null($this->listener)) {
                $cb = $this->listener;
                $cb($return);
            }

            return $return;
        }
    }
}
