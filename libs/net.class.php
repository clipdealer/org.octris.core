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
         * Curl multi handle, if currently executed, otherwise null.
         *
         * @octdoc  p:net/$mh
         * @var     resource|null
         */
        protected $mh = null;
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
         * Session queue.
         *
         * @octdoc  p:net/$queue
         * @var     array
         */
        protected $queue = array();
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
            if (!is_null($this->mh)) {
                $this->clients[] = $client;
            } else {
                // push directly into queue
            }

            return $client;
        }

        /**
         * Execute registered clients.
         *
         * @octdoc  m:net/execute
         */
        public function execute()
        /**/
        {
            if (!is_null($this->mh)) {
                throw new \Execution('Session is currently beeing executed');
            }

            $this->queue = $this->clients;

            $active = null;

            $this->mh = curl_multi_init();

            for ($i = 0; $i < $this->sessions; ++$i) {
                if (!($client = array_unshift($this->clients))) break;

                $ch = curl_init();
                curl_setopt_array($ch, $client->getOptions());

                curl_multi_add_handle($ch);
            }

            do {
                curl_multi_select($this->mh);

                while (($result = curl_multi_exec($this->mh, $active)) == CURLM_CALL_MULTI_PERFORM);

                if ($result != CURLM_OK) break;

                while (($done = curl_multi_info_read($this->mh))) {
                    // code that parses, adds or removes links to mcurl
                    print "ADD\n";
                    print_r($done);
                }

                print "loop\n";
                die;
            } while(true);

            curl_multi_close($this->mh);
            $this->mh = null;
        }
    }
}
