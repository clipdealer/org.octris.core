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
         * Stores query parameters.
         *
         * @octdoc  p:uri/$query
         * @var     \ArrayObject
         */
        protected $query;
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
            
            $this->components = parse_url($uri);

            if (isset($this->components['query'])) {
                $args = array();
                parse_str($this->components['query'], $args);
                
                $this->query = new \ArrayObject($args);
            } else {
                $this->query = new \ArrayObject();
            }

            // TODO: parse host to subdomain(s) + 2nd level domain + tld and store it in components
            //       see: http://www.dkim-reputation.org/regdom-libs/, http://publicsuffix.org/
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
            return $this->buildUri();
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
            if ($name == 'query') {
                $component = $this->query;
            } else {
                $component = (isset($this->components[$name])
                                ? $this->components[$name]
                                : '');
            }
            
            return $component;
        }

        /**
         * Setter for URI components.
         *
         * @octdoc  m:uri/__set
         * @param   string          $name               Component of URI to set.
         * @param   mixed           $value              Value to set for component.
         */
        public function __set($name, $value)
        /**/
        {
            if ($name == 'query') {
                throw new \Exception('Overwriting of "query" is not allowed');
            } elseif (!array_key_exists($name, $this->components))  {
                throw new \Exception(sprintf('Unknown URI component "%s"', $name));
            } else {
                // TODO: validate value regarding to component type?
                $this->components[$name] = $value;
            }
        }

        /**
         * This method is called when the object is casted to a string.
         *
         * @octdoc  m:uri/buildUri
         * @return  string                              URI.
         */
        protected function buildUri()
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
                
                (isset($this->components['port']) 
                    ? ':' . $this->components['port']
                    : '') .
                
                '/' .
                
                (isset($this->components['path'])
                    ? ltrim($this->components['path'], '/')
                    : '') .
                
                (isset($this->components['query'])
                    ? '?' . http_build_query((array)$this->query)
                    : '') . 
                    
                (isset($this->components['fragment'])
                    ? '#' . $this->components['fragment']
                    : '');

            return $this->uri;
        }
    }
}
