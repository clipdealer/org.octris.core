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
     * @octdoc      c:net/client
     * @copyright   Copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    abstract class client
    /**/
    {
        /**
         * Supported schemes. Is empty, if there is no limitation for 
         * protocols.
         *
         * @octdoc  p:client/$schemes
         * @var     array
         */
        protected static $schemes = array();
        /**/

        /**
         * Curl info.
         *
         * @octdoc  p:client/$info
         * @var     array
         */
        protected static $info = null;
        /**/

        /**
         * Options for curl client.
         *
         * @octdoc  p:client/$options
         * @var     array
         */
        protected $options = array();
        /**/

        /**
         * Information of the last request performed.
         *
         * @octdoc  p:client/$info
         * @var     array
         */
        protected $info = array();
        /**/

        /**
         * Session assigned to the client.
         *
         * @octdoc  p:client/$session
         * @var     \org\octris\core\net|null
         */
        protected $session = null;
        /**/

        /**
         * Event listener.
         * 
         * @octdoc  p:client/$listener
         * @var     callable|null
         */
        protected $listener = null;
        /**/

        /**
         * Client URI.
         *
         * @octdoc  p:client/$uri
         * @var     \org\octris\core\type\uri
         */
        protected $uri;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:client/__construct
         * @param   \org\octris\core\type\uri       $uri            URI 
         */
        public function __construct(\org\octris\core\type\uri $uri)
        /**/
        {
            if (!extension_loaded('curl')) {
                throw new \Exception('Missing ext/curl');
            }

            if (is_null(self::$info)) {
                self::$info = curl_version();
            }

            if (count(static::$schemes) > 0 && !in_array($uri->scheme, static::$schemes)) {
                throw new \Exception(sprintf(
                    'Invalid URI specified, supported protocols are "%s"',
                    implode(', ', static::$schemes)
                ));
            }

            $this->uri = clone $uri;

            $this->options[CURLOPT_RETURNTRANSFER] = true;
        }

        /**
         * Clone.
         *
         * @octdoc  m:client/__clone
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
            $this->options[CURLOPT_URL] = (string)$this->uri;

            return $this->options;
        }

        /**
         * Add curl client to a session.
         *
         * @octdoc  m:client/setSession
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
         * @octdoc  m:client/getSession
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
         * @octdoc  m:client/setListener
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
         * @octdoc  m:client/getListener
         * @return  callable                                Listener set.
         */
        public function getListener()
        /**/
        {
            return $this->listener;
        }

        /**
         * Set a function for handling response headers.
         *
         * @octdoc  m:client/setHeaderCallback
         * @param   callable            $callback           Callback to call for response headers.
         */
        public function setHeaderCallback(callable $callback)
        /**/
        {
            $this->options[CURLOPT_HEADERFUNCTION] = $callback;
        }
        
        /**
         * Set a function for handling response body.
         *
         * @octdoc  m:client/setBodyCallback
         * @param   callable            $callback           Callback to call for response body.
         */
        public function setBodyCallback(callable $callback)
        /**/
        {
            $this->options[CURLOPT_WRITEFUNCTION] = $callback;
        }

        /**
         * Execute client.
         *
         * @octdoc  m:client/execute
         */
        public function execute()
        /**/
        {
            if (!is_null($this->session)) {
                throw new \Exception('Unable to execute a client that is assigned to a session');
            }
    
            $ch = curl_init();
            curl_setopt_array($ch, $this->getOptions());

            $return = curl_exec($ch);

            $this->info = curl_getinfo($ch);

            curl_close($ch);

            if (!is_null($this->listener)) {
                $cb = $this->listener;
                $cb($return);
            }

            return $return;
        }
    }
}
