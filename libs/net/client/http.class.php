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
         * Request method.
         *
         * @octdoc  p:http/$body
         * @var     string
         */
        protected $method;
        /**/

        /**
         * Stores response headers of last request.
         *
         * @octdoc  p:http/$response_headers
         * @var     array
         */
        protected $response_headers = array();
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:http/__construct
         * @param   \org\octris\core\type\uri       $url            Valid http(s) URL.
         * @param   string                          $method         Optional HTTP Method to use, default is GET.
         */
        public function __construct(\org\octris\core\type\uri $url, $method = self::T_GET)
        /**/
        {
            switch ($this->method = strtoupper($method)) {
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
                $this->options[CURLOPT_CUSTOMREQUEST] = $this->method;
                break;
            default:
                throw new \Exception(sprintf('Unknown request method "%s"', $this->method));
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
         * Get headers of response.
         *
         * @octdoc  m:http/getResponseHeaders
         * @return  array                                           Response headers.
         */
        public function getResponseHeaders()
        /**/
        {
            return $this->response_headers;
        }
        
        /**
         * Get status code of last request.
         *
         * @octdoc  m:http/getStatus
         * @return  int                                                 HTTP status code.
         */
        public function getStatus()
        /**/
        {
            return (isset($this->request_info['http_code'])
                    ? $this->request_info['http_code']
                    : null);
        }

        /**
         * Return content type of last request.
         *
         * @octdoc  m:http/getContentType
         * @return  string                                              Content type.
         */
        public function getContentType()
        /**/
        {
            return (isset($this->request_info['content_type'])
                    ? $this->request_info['content_type']
                    : null);
        }

        /**
         * Set a function for handling response body.
         *
         * @octdoc  m:http/setBodyCallback
         * @param   callable                        $callback       Callback to call for response body.
         */
        public function setBodyCallback(callable $callback)
        /**/
        {
            $this->options[CURLOPT_WRITEFUNCTION] = $callback;
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

        /**
         * Execute http client.
         *
         * @octdoc  m:http/execute
         * @return  string                                  Response.
         */
        public function execute()
        /**/
        {
            // setup buffer for storing response headers
            $buf_headers = new \org\octris\core\net\buffer();
            $this->options[CURLOPT_HEADERFUNCTION] = function($ch, $data) use ($buf_headers) {
                $data = preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $data);
                
                return $buf_headers->write($data);
            };

            // execute request
            $return = parent::execute();
            
            // process response headers
            $this->response_headers = static::parseResponseHeaders($buf_headers);
            
            unset($buf_headers);
            
            return $return;
        }
        
        /**
         * Parse response headers.
         *
         * @octdoc  p:http/parseResponseHeaders
         * @param   \org\octris\core\net\buffer             $buffer                 Instance of buffer to parse content of.
         * @return  array                                                           Contains parsed headers.
         */
        protected static function parseResponseHeaders(\org\octris\core\net\buffer $buffer)
        /**/
        {
            $headers = array();
            
            foreach ($buffer as $header) {
                if (preg_match('/^([^:]+?): (.+)/', $header, $match)) {
                    $name  = strtolower($match[1]);
                    $value = trim($match[2]);
                    
                    if (isset($headers[$name])) {
                        if (!is_array($headers[$name])) {
                            $headers[$name] = array($headers[$name]);
                        }
                        
                        $headers[$name][] = $value;
                    } else {
                        $headers[$name] = $value;
                    }
                }
            }
            
            return $headers;
        }
    }
}
