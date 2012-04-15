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
     * cURL wrapper class.
     * 
     * @octdoc      c:core/net
     * @copyright   Copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class net
    /**/
    {
        /**
         * cURL handler.
         *
         * @octdoc  p:net/$ch
         * @var     resource
         */
        protected $ch;
        /**/

        /**
         * Max. concurrent sessions.
         *
         * @octdoc  p:net/$sessions
         * @var     int
         */
        protected $sessions;
        /**/

        /**
         * The clients.
         *
         * @octdoc  p:net/$clients
         * @var     array
         */
        protected $clients = array();
        /**/

        /**
         * cURL options.
         *
         * @octdoc  p:net/$options
         * @var     array
         */
        protected $options = array();
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:net/__construct
         * @param   int             $sessions                       Maximum concurrent sessions.
         */
        public function __construct($sessions = 10)
        /**/
        {
            $this->sessions = $sessions;
        }

        /**
         * Add a network transport client to the session.
         *
         * @octdoc  m:net/addClient
         * @param   \org\octris\core\net\client     $client         Client to add to session.
         * @return  \org\octris\core\net\client                     The client instance.
         */
        public function addClient(\org\octris\core\net\client $client)
        /**/
        {
            $client->setSession($this);

            $this->clients[] = $clients;

            return $clients;
        }

        /**
         * Execute registered clients.
         *
         * @octdoc  m:net/execute
         */
        public function execute()
        /**/
        {
            $queue = array();

            do {
                // $ch = curl_init();
                // curl_setopt_array($ch, $this->options);

                // $return = curl_exec($ch);

                // curl_close($ch);

            } while(true);
        }
    }
}
