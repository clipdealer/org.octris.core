<?php

namespace org\octris\core {
    /**
     * This class is used to wrap the PHP superglobals.
     *
     * @octdoc      c:core/request
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class request
    /**/
    {
        /**
         * Wrapped arrays storage.
         *
         * @octdoc  v:request/$wrapped
         * @var     array
         */
        protected static $wrapped = array();
        /**/
        
        /**
         * Name of wrapped array to access with the instance.
         *
         * @octdoc  v:request/$name
         * @var     string
         */
        protected $name = '';
        /**/
        
        /**
         * Constructor.
         *
         * @octdoc  m:request/__construct
         * @param   
         */
        protected function __construct($name)
        /**/
        {
            $this->name = $name;
        }
        
        /**
         * Return data of specified name from wrapped data.
         *
         * @octdoc  m:request/get
         * @param   string      $name           Name of value to return
         * @return  value
         */
        public function get($name)
        /**/
        {
            return self::$wrapped[$this->name][$name];
        }
        
        /**
         * Apply validation schema to data.
         *
         * @octdoc  m:request/validate
         * @param   array       $schema         Validation schema.
         */
        public function validate(array $schema)
        /**/
        {
            
        }
        
        /**
         * Access wrapped data.
         *
         * @octdoc  m:request/__callStatic
         * @param   string      $name           Name of wrapped data to access.
         */
        public static function __callStatic($name, $args)
        /**/
        {
            if (isset(self::$wrapped[$name])) {
                return new static($name);
            }
        }
        
        /**
         * Wrap a superglobal or any other array.
         *
         * @octdoc  m:request/wrap
         * @param   string      $name           Name to wrap array as.
         * @param   array       $data           Array to wrap.
         * @return  null                        Assign result to superglobal to prevent access to it.
         */
        public static function wrap($name, $data)
        /**/
        {
            if (isset(self::$wrapped[$name])) {
                throw new \Exception('name is already taken');
            }
            
            self::$wrapped[$name] = $data;
            
            return null;
        }
        
        //******//
        
        function addValidation($name, $schema) {
            $me = $this;
            
            $this->validator[$name] = function($data) use ($me) {
                static $return = null;
                static $errors = array();
                
                if (is_null($return)) {
                    $schema = new \org\octris\core\schema(...);

                    $return = $schema->validate($data);      // returns sanitized data or false, if validation failed
                    
                    $me->addErrors($schema->getErrors());
                }
                
                return $return;
            };
        }
        
        function validate($name, $data) {
            if (!($return = $this->validate[$name]($data))) {
                ...
            }
            
            return $return;
        }
    }
    
    $_ENV = request::wrap('env', $_ENV);
    
    request::addValidation('env', function($data) {
        $schema = new \org\octris\core\schema(...);
        
        return = $schema->validate($data);
    });
    
    print_r(array('_ENV:', $_ENV));
    
    print request::get('env')->env()->get('OCTRIS_BASE');
}

