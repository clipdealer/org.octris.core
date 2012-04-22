<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\net\client {
    /**
     * HTTP class.
     * 
     * @octdoc      c:client/http
     * @copyright   Copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class http extends \org\octris\core\net\client
    /**/
    {
        /**
         * HTTP Methods
         * 
         */
        const T_CONNECT = 'CONNECT';
        const T_DELETE  = 'DELETE';
        const T_GET     = 'GET';
        const T_HEAD    = 'HEAD';
        const T_OPTIONS = 'OPTIONS';
        const T_POST    = 'POST';
        const T_PUT     = 'PUT';
        const T_TRACE   = 'TRACE';
        /**/

        /**
         * Supported schemes. Is empty, if there is no limitation for 
         * protocols.
         *
         * @octdoc  p:http/$schemes
         * @var     array
         */
        protected static $schemes = array('http', 'https');
        /**/

        /**
         * Post body/arguments
         *
         * @octdoc  p:http/$post
         * @var     null
         */
        protected $post;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:http/__construct
         * @param   \org\octris\core\type\uri       $url            Valid http(s) URL.
         * @param   string                          $method         Optional HTTP Method to use, default is GET.
         */
        public function __construct(\org\octris\core\type\uri $url, $methods = self::T_GET)
        /**/
        {
            switch ($methods = strtoupper($methods)) {
            case self::T_GET:
                $this->options[CURLOPT_HTTPGET] = true;
                break;
            case self::T_POST:
                $this->options[CURLOPT_POST] = true;
                break;
            case self::T_PUT:
                $this->options[CURLOPT_PUT] = true;
                break;
            case self::T_CONNECT:
            case self::T_DELETE:
            case self::T_HEAD:
            case self::T_OPTIONS:
            case self::T_TRACE:
                $this->options[CURLOPT_CUSTOMREQUEST] = $methods;
                break;
            default:
                throw new \Exception(sprintf('Unknown request method "%s"', $methods));
                break;
            }
            
            $this->options[CURLOPT_PROTOCOLS]  = CURLPROTO_HTTP | CURLPROTO_HTTPS;
            $this->options[CURLOPT_HTTPHEADER] = array();

            parent::__construct($url);
        }

        /**
         * Set a HTTP request header.
         *
         * @octdoc  m:http/addHeader
         */
        public function addHeader($name, $content)
        /**/
        {
            switch (strtolower($name)) {
            case 'user-agent':
                $this->setAgent($content);
                break;
            case 'referer':
                $this->setReferer($content);
                break;
            default:
                $this->options[CURLOPT_HTTPHEADER][$name] = $content;
                break;
            }
        }

        /**
         * Set maximum redirects for following HTTP location header redirects.
         *
         * @octdoc  m:http/setMaxRedirects
         * @param   int                             $num            Maximum number of redirects.
         * @param   bool                            $autoreferer    Optional whether to auto-set the referer for redirects.
         */
        public function setMaxRedirects($num, $autoreferer = true, $auth = false)
        /**/
        {
            $this->options[CURLOPT_FOLLOWLOCATION]    = true;
            $this->options[CURLOPT_MAXREDIRS]         = $num;
            $this->options[CURLOPT_AUTOREFERER]       = $autoreferer;
        }

        /**
         * Set user agent string.
         *
         * @octdoc  m:http/setAgent
         * @param   string                          $agent          Agent to set.
         */
        public function setAgent($agent)
        /**/
        {
            $this->options[CURLOPT_USERAGENT] = $agent;
        }

        /**
         * Set HTTP authentication.
         *
         * @octdoc  m:http/setAuthentication
         */
        public function setAuthentication($username, $password, $method, $auth = false)
        /**/
        {
            $this->options[CURLOPT_USERPWD]           = $username . ':' . $password;
            $this->options[CURLOPT_HTTPAUTH]          = $method;
            $this->options[CURLOPT_UNRESTRICTED_AUTH] = $auth;
        }

        /**
         * Set HTTP referer.
         *
         * @octdoc  m:http/setReferer
         * @param   string                          $referer        Referer to set.
         */
        public function setReferer($referer)
        /**/
        {
            $this->options[CURLOPT_REFERER] = $referer;
        }
    }
}
