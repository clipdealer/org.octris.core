<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\type {
    /**
     * URL parser and pseudo-type.
     *
     * @octdoc      c:type/url
     * @copyright   copyright (c) 2010-2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class url
    /**/
    {
        /**
         * Stores URL.
         *
         * @octdoc  v:url/$url
         * @var     string
         */
        protected $url = '';
        /**/
        
        /**
         * Stores URL components.
         *
         * @octdoc  v:url/$components
         * @var     array
         */
        protected $components = array();
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:url/__construct
         * @param   string          $url                URL to parse and store.
         */
        public function __construct($url)
        /**/
        {
            $this->url = $url;
            
            $this->components = parse_url($url);
        }
        
        /**
         * Returns stored URL when object instance is casted to a string.
         *
         * @octdoc  m:url/__toString
         * @return  string                              Stored URL.
         */
        public function __toString()
        /**/
        {
            return $this->url;
        }

        /**
         * Getter for URL components.
         *
         * @octdoc  m:url/__get
         * @param   string          $name               Component of URL to return.
         */
        public function __get($name)
        /**/
        {
            $component = (isset($this->components[$name])
                            ? $this->components[$name]
                            : '');
            
            return $component;
        }

        /**
         * This method is called internally after modification of any URL component to update the URL string.
         *
         * @octdoc  m:url/updateUrl
         */
        protected function updateUrl()
        /**/
        {
            $this->url = 
                (isset($this->components['scheme'])
                    ? $this->components['scheme'] . '://'
                    : '') .
                
                (isset($this->components['user'])
                    ? $this->components['user'] . (isset($this->components['pass'])
                                                    ? ':' . $this->components['pass']
                                                    : '') . '@'
                    : '') .
                    
                $this->components['host'] .
                
                (isset(this->components['port']) 
                    ? ':' . $this->components['port']
                    : '') .
                
                '/' .
                
                (isset($this->components['path'])
                    ? ltrim($this->components['path'], '/')
                    : '') .
                
                (isset($this->components['query'])
                    ? '?' . $this->components['query']
                    : '') . 
                    
                (isset($this->components['fragment'])
                    ? '#' . $this->components['fragment']
                    : '');
        }
        
        /**
         * Returns top-level-domain (TLD) of stored URL.
         *
         * @octdoc  m:url/getTld
         * @return  string                                  Top-level-domain.
         */
        public function getTld()
        /**/
        {
            $host = $this->components['host'];
            $tld = substr($host, strrpos('.', $host));
            
            return $tld;
        }

        /**
         * Return query parsed to an array.
         *
         * @octdoc  m:url/parseQuery
         * @return  array                                   Array representation of query parameters.
         */
        public function parseQuery()
        /**/
        {
            return parse_str($this->components['query']);
        }

        /**
         * Build query string from provided array.
         *
         * @octdoc  m:url/buildQuery
         * @param   array           $data                   Data to build query from.
         */
        public function buildQuery(array $data)
        /**/
        {
            $this->components['query'] = http_build_query($data);
            
            $this->updateUrl();
        }

        /**
         * Return stored URL.
         *
         * @octdoc  m:url/getUrl
         * @return  string                                  Stored URL.
         */
        public function getUrl()
        /**/
        {
            return $this->url;
        }
    }
}
