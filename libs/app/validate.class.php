<?php

namespace org\octris\core\app {
    /**
     * Validation class for applications.
     *
     * octdoc       c:app/validate
     * @copyright   copyright (c) 2010-2011 by Harald Lapp
     * @author      Harald Lapp <harald.lapp@gmail.com>
     */
    class validate 
    /**/
    {
        /**
         * Instance.
         *
         * @octdoc  v:validate/$instance
         * @var     \org\octris\core\app\validate
         */
        private $instance = null;
        /**/
        
        /**
         * Protected constructor and magic clone method to prevent existance of multiple instances.
         *
         * @octdoc  m:validate/__construct, __clone
         */
        protected function __construct() {}
        protected function __clone() {}
        /**/
         
        /**
         * Return instance of validate object.
         *
         * @octdoc  m:validate/getInstance
         * @return  \org\octris\core\validate       Instance of validate object.
         */
        public final function getInstance()
        /**/
        {
            if (is_null(self::$instance)) {
                self::$instance = new static();
            }
            
            return self::$instance;
        }
        
        /**
         * Calculate a key based on a page object and an action.
         *
         * @octdoc  m:validate/getKey
         * @param   \org\octris\core\page   $page       Instance of some page.
         * @param   string                  $action     Name of an action.
         */
        public function getKey(\org\octris\core\page $page, $action)
        /**/
        {
            return get_class($page) . '.' . $action;
        }

        /**
         * Return a registered validation sche,a
         *
         * @octdoc  m:validate/getSchema
         * @param   \org\octris\core\page   $page       Instance of page the ruleset was registered for.
         * @param   string                  $action     Name of action the ruleset was registered for.
         * @return  array                               Registered schema or empty array.
         */
        public function getSchema(\org\octris\core\page $page, $action)
        /**/
        {
            $key    = $this->getKey($page, $action);
            $return = array();

            if (isset($this->rulesets[$key])) {
                $return = $this->rulesets[$key]['schema'];
            }

            return $return;
        }

        /**
         * Prepare schema to somehow validate it and make it more easy to work with it.
         *
         * @octdoc  m:validate/prepareSchema
         * @param   array                       §schema         Validation schema
         * @return  array                                       Prepared validation schema
         */
        public function prepareSchema(array $schema)
        /**/
        {
            $schema = (!isset($schema['default']) && isset($schema['type'])
                       ? array('default' => $schema)
                       : $schema);
            
            if (!isset($schema['default']['type']) || $schema['default']['type'] != \org\octris\core\validate::T_OBJECT || !isset($schema['default']['type']['properties'])) {
                throw new \Exception('invalid validation scheme');
            }
            
            return $schema;
        }
        
        /**
         * Register validation schema.
         *
         * @octdoc  m:validate/addSchema
         * @param   \org\octris\core\page       $page       Instance of page ruleset applies to.
         * @param   \org\octris\core\wrapper    $wrapper    Instance of wrapped parameters to validate.
         * @param   array                       $schema     Validation schema.
         * @param   int                         $mode       Validation mode.
         */
        public function addSchema(\org\octris\core\page $page, $action, \org\octris\core\wrapper $wrapper, array $schema, $mode = \org\octris\core\validate\schema::T_STRICT)
        /**/
        {
            $key = $this->getKey($page, $action);

            $this->rulesets[$key] = array(
                'wrapper' => $wrapper,
                'schema'  => $this->prepareSchema($schema),
                'mode'    => $mode
            );
        }
        
        /**
         * Apply registered validation ruleset.
         *
         * @octdoc  m:validate/validate
         * @param   \org\octris\core\page       $page       Instance of page object of registered ruleset.
         * @param   string                      $action     Action of registered ruleset.
         * @param   \org\octris\core\page       $errors     Optional second page to send errors to.
         */
        public function validate(\org\octris\core\page $page, $action, \org\octris\core\page $errors = null)
        /**/
        {
            $key = $this->getKey($page, $action);
            $ret = true;

            if (isset($this->rulesets[$key])) {
                if (is_null($errors)) $errors = $page;
                
                $properties =& $this->ruleset[$key]['default']['properties'];
                $wrapper    = $ruleset['wrapper'];
                
                foreach ($properties as $name => $schema) {
                    if (!isset($wrapper[$name])) {
                        if (isset($schema['required'])) {
                            $errors->addError($schema['required']);
                        }
                    } elseif (!$wrapper[$name]->validate($schema)) {
                        $ret = false;
                        
                        if (isset($schema['invalid'])) {
                            $errors->addError($schema['invalid']);
                        }
                    }
                }
            }

            return $ret;
        }
    }
}
