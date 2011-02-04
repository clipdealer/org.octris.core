<?php

namespace org\octris\core {
    /**
     * Data provider.
     *
     * @octdoc      c:core/provider
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class provider
    /**/
    {
        /**
         * Flags.
         * 
         * @octdoc  d:provider/T_READONLY
         */
        const T_READONLY = 1;
        /**/
        
        /**
         * Internal data storage
         *
         * @octdoc  v:provider/$storage
         * @var     array
         */
        protected static $storage = array();
        /**/
        
        /**
         * Stores meta information about validation state.
         *
         * @octdoc  v:provider/$meta
         * @var     array
         */
        protected $meta = array();
        /**/
        
        /**
         * Data validators
         *
         * @octdoc  v:provider/$validators
         * @var     array
         */
        protected $validators = array();
        /**/
        
        /**
         * Stores validation flags and sanitized values.
         *
         * @octdoc  v:provider/$validated
         * @var     array
         */
        protected $validated = array();
        /**/

        /**
         * Stores name of data that is granted access to by instance.
         *
         * @octdoc  v:provider/$name
         * @var     string
         */
        protected $name = null;
        /**/
        
        /**
         * Constructor.
         *
         * @octdoc  m:provider/__construct
         * @param   string          $name               Name of data to grant access to.
         */
        protected function __construct($name)
        /**/
        {
            $this->name = $name;
        }

        /**
         * Returns a new instance of the data provider by granting access to
         * data stored as the specified name in the data provider.
         *
         * @octdoc  m:provider/access
         * @param   string          $name               Name of data to access.
         */
        public static function access($name)
        /**/
        {
            $return = null;
            
            if (isset(self::$storage[$name])) {
                $return = self::$storage[$name];
            }
            
            return $return;
        }

        /**
         * Save data in provder.
         *
         * @octdoc  m:provider/set
         * @param   string          $name               Name to store data as.
         * @param   array           $data               Data to store.
         * @param   int             $flags              Optional OR-able flags.
         */
        public static function set($name, array $data, $flags = 0)
        /**/
        {
            if (isset(self::$storage[$name]) && (self::$storage[$name]['flags'] & self::T_READONLY) == self::T_READONLY) {
                throw new \Exception("access to data '$name' is readonly");
            }
            
            self::$storage[$name] = array(
                'data'  => $data,
                'flags' => $flags,
            );
        }

        /**
         * Add a validation schema.
         *
         * @octdoc  m:provider/addValidator
         * @param   string          $name               Name of validator.
         * @param   array           $schema             Validation schema.
         */
        public function addValidator($name, array $schema)
        /**/
        {
            $this->validators[$name] = function($data) use ($schema) {
                static $return = null;
                
                if (is_null($return)) {
                    $schema = new \org\octris\core\validate\schema($schema);
                    $data   = $schema->validate($data);                         // returns sanitized data
                    $errors = $schema->getErrors();
                    
                    $return = array(
                        (count($errors) == 0),
                        $data,
                        $errors 
                    );
                }

                return $return;
            };
        }

        /**
         * Returns (validated and sanitized) stored data validated with specified 
         * validator name.
         *
         * @octdoc  m:provider/getData
         * @param   string          $name               Name of validator to apply.
         * @param   array           $errors             Optional array may be specified to fetch validation errors.
         * @return  array                               Validated and sanitized data.
         */
        public function applyValidator($name, array &$errors = array())
        /**/
        {
            if (!isset($this->validators[$name])) {
                throw new \Exception("unknown validator '$name'");
            }
            
            $return = $this->validators[$name](self::$storage[$this->name]['data']);
            
            return $return;
        }

        /**
         * Test if the data field of the specified name is available.
         *
         * @octdoc  m:provider/isExist
         * @param   string          $name               Name of data field to test.
         * @return  bool                                Returns true if data field is available.
         */
        public function isExist($name)
        /**/
        {
            return (isset(self::$storage[$this->name]['data'][$name]));
        }

        /**
         * Validate a stored data field with specified validator type and options.
         *
         * @octdoc  m:provider/isValid
         * @param   string          $name               Name of data field to validate.
         * @param   string          $type               Validation type.
         * @param   array           $options            Optional settings for validation.
         * @return  bool                                Returns true if validation succeeded.
         */
        public function isValid($name, $type, array $options = array())
        /**/
        {
            return ($this->getValue($name, $type, $options) !== false);
        }
        
        /**
         * Validates a specified data field and returns it, if it's valid.
         *
         * @octdoc  m:proivder/getValue
         * @param   string          $name               Name of data field to validate.
         * @param   string          $type               Validation type.
         * @param   array           $options            Optional settings for validation.
         * @return  mixed                               Returns value or false if validation failed.
         */
        public function getValue($name, $type, array $options = array())
        /**/
        {
            $key = md5(serialize(array($name, $type, $options)));

            if (!isset($this->validated[$key])) {
                $validator = null;
            
                if (is_scalar($type) && class_exists($type)) {
                    $validator = new $type($options);
                }
                
                if (!($validator instanceof \org\octris\core\validation\type)) {
                    throw new \Exception("'$type' is not a validation type");
                }

                if (($is_valid = isset(self::$storage[$this->name]['data'][$name]))) {
                    $value = self::$storage[$this->name]['data'][$name];
                    $value = $validator->preFilter($value);

                    $this->validated[$key] = array(
                        'value'     => $value,
                        'is_valid'  => $validator->validate($value)
                    );
                }
            }

            return ($this->validated[$key]['is_valid']
                    ? $this->validated[$key]['value']
                    : false);
        }
        
        /**
         * Set a specified data field with a value.
         *
         * @octdoc  m:provider/setValue
         * @param   string          $name           Name of data field to set.
         * @param   mixed           $value          Value to set for data field.
         * @param   string          $type           Validation type for data field.
         * @param   array           $options        Optional settings for validation.
         * @return  bool                            Returns false if validation failed.
         */
        public function setValue($name, $value, $type, $options)
        /**/
        {
            $key = md5(serialize(array($name, $type, $options)));

            if ((self::$storage[$this->name]['flags'] & self::T_READONLY) == self::T_READONLY) {
                throw new \Exception("access to data '$this->name' is readonly");
            }
            
            $validator = null;
        
            if (is_scalar($type) && class_exists($type)) {
                $validator = new $type($options);
            }
            
            if (!($validator instanceof \org\octris\core\validation\type)) {
                throw new \Exception("'$type' is not a validation type");
            }

            self::$storage[$this->name]['data'][$name] = $value;
            
            return ($this->isValid($name, $type, $options));
        }
    }
}
