<?php

namespace org\octris\core\type {
    /****c* type/url
     * NAME
     *      url
     * FUNCTION
     *      url parser
     * COPYRIGHT
     *      copyright (c) 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */

    class url {
        /****v* url/$url
         * SYNOPSIS
         */
        protected $url = '';
        /*
         * FUNCTION
         *      URL
         ****
         */
         
        /****v* url/$components
         * SYNOPSIS
         */
        protected $components = array();
        /*
         * FUNCTION
         *      stores URL components
         ****
         */
        
        /****m* url/__construct
         * SYNOPSIS
         */
        public function __construct($url)
        /*
         * FUNCTION
         *      constructor
         * INPUTS
         *      * $url (string) -- URL to store
         ****
         */
        {
            $this->url;
            
            $this->components = parse_url($url);
        }
        
        /****m* url/__toString
         * SYNOPSIS
         */
        public function __toString()
        /*
         * FUNCTION
         *      return URL
         * OUTPUTS
         *      (string) -- stored URL
         ****
         */
        {
            return $this->url;
        }
        
        /****m* url/__get
         * SYNOPSIS
         */
        public function __get($name)
        /*
         * FUNCTION
         *      return component of URL
         * INPUTS
         *      * $name (string) -- name of URL component to return
         * OUTPUTS
         *      (string) -- URL component
         ****
         */
        {
            $component = (isset($this->components[$name])
                            ? $this->components[$name]
                            : '');
            
            return $component;
        }
        
        /****m* url/updateUrl
         * SYNOPSIS
         */
        protected function updateUrl()
        /*
         * FUNCTION
         *      this method is called internally after modification of any URL component to update the URL string
         ****
         */
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
        
        /****m* url/getTld
         * SYNOPSIS
         */
        public function getTld()
        /*
         * FUNCTION
         *      return top-level-domain (TLD)
         * OUTPUTS
         *      (string) -- toplevel domain
         ****
         */
        {
            $host = $this->components['host'];
            $tld = substr($host, strrpos('.', $host));
            
            return $tld;
        }
        
        /****m* url/parseQuery
         * SYNOPSIS
         */
        public function parseQuery()
        /*
         * FUNCTION
         *      return query parsed into an array
         * OUTPUTS
         *      (array) -- array of query components
         ****
         */
        {
            return parse_str($this->components['query']);
        }
        
        /****m* url/buildQuery
         * SYNOPSIS
         */
        public function buildQuery(array $data)
        /*
         * FUNCTION
         *      build query from provided array
         * INPUTS
         *      * $data (array) -- data to build query from
         ****
         */
        {
            $this->components['query'] = http_build_query($data);
            
            $this->updateUrl();
        }
        
        /****m* url/getUrl
         * SYNOPSIS
         */
        public function getUrl()
        /*
         * FUNCTION
         *      return URL
         * OUTPUTS
         *      (string) -- URL
         ****
         */
        {
            return $this->url;
        }
    }
}
