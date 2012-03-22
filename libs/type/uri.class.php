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
     * URI parser and pseudo-type.
     *
     * @octdoc      c:type/uri
     * @copyright   copyright (c) 2010-2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class uri
    /**/
    {
        /**
         * Stores URI.
         *
         * @octdoc  p:uri/$uri
         * @var     string
         */
        protected $uri = '';
        /**/
        
        /**
         * Stores URI components.
         *
         * @octdoc  p:uri/$components
         * @var     array
         */
        protected $components = array();
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:uri/__construct
         * @param   string          $uri                URI to parse and store.
         */
        public function __construct($uri)
        /**/
        {
            $this->uri = $uri;
            
            $this->components = parse_uri($uri);
        }
        
        /**
         * Returns stored URI when object instance is casted to a string.
         *
         * @octdoc  m:uri/__toString
         * @return  string                              Stored URI.
         */
        public function __toString()
        /**/
        {
            return $this->uri;
        }

        /**
         * Getter for URI components.
         *
         * @octdoc  m:uri/__get
         * @param   string          $name               Component of URI to return.
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
         * This method is called internally after modification of any URI component to update the URI string.
         *
         * @octdoc  m:uri/updateUri
         */
        protected function updateUri()
        /**/
        {
            $this->uri = 
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
         * Returns top-level-domain (TLD) of stored URI.
         *
         * @octdoc  m:uri/getTld
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
         * @octdoc  m:uri/parseQuery
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
         * @octdoc  m:uri/buildQuery
         * @param   array           $data                   Data to build query from.
         */
        public function buildQuery(array $data)
        /**/
        {
            $this->components['query'] = http_build_query($data);
            
            $this->updateUri();
        }

        /**
         * Return stored URI.
         *
         * @octdoc  m:uri/getUri
         * @return  string                                  Stored URI.
         */
        public function getUri()
        /**/
        {
            return $this->uri;
        }
    }
}
