<?php

namespace org\octris\core {
    /**
     * Validation base class.
     *
     * octdoc       c:core/validate
     * @copyright   copyright (c) 2010-2011 by Harald Lapp
     * @author      Harald Lapp <harald.lapp@gmail.com>
     */
    class validate 
    /**/
    {
        /**
         * Instance of validate object.
         *
         * @octdoc  v:validate/$instance
         * @var     \org\octris\core\validate
         */
        private $instance = null;
        /**/
        
        /**
         * Schema structure types.
         *
         * @octdoc  d:validate/T_OBJECT, T_ARRAY
         */
        const T_OBJECT = 1;
        const T_ARRAY  = 2;
        /**/
         
        /**
         * Available validation types.
         *
         * @octdoc  d:validate/T_ALPHA, T_ALPHANUM, T_BOOL, T_CALLBACK, T_DIGIT, T_PATH, T_PRINTABLE, T_PROJECT, T_XDIGIT
         */
        const T_ALPHA     = '\org\octris\core\validate\type\alpha';
        const T_ALPHANUM  = '\org\octris\core\validate\type\alphanum';
        const T_BOOL      = '\org\octris\core\validate\type\bool';
        const T_CALLBACK  = '\org\octris\core\validate\type\callback';
        const T_CHAIN     = '\org\octris\core\validate\type\chain';
        const T_DIGIT     = '\org\octris\core\validate\type\digit';
        const T_PATH      = '\org\octris\core\validate\type\path';
        const T_PATTERN   = '\org\octris\core\validate\type\pattern';
        const T_PRINTABLE = '\org\octris\core\validate\type\printable';
        const T_PROJECT   = '\org\octris\core\validate\type\project';
        const T_XDIGIT    = '\org\octris\core\validate\type\xdigit';
        const T_URL       = '\org\octris\core\validate\type\url';
        /**/
        
        /**
         * Private constructor and magic clone method to prevent existance of multiple instances.
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
         * Return a registered validation ruleset.
         *
         * @octdoc  m:validate/getRuleset
         * @param   \org\octris\core\page   $page       Instance of page the ruleset was registered for.
         * @param   string                  $action     Name of action the ruleset was registered for.
         * @return  array                               Ruleset. array is empty, if no ruleset for specified 
         *                                              properties was registered.
         */
        public function getRuleset(\org\octris\core\page $page, $action)
        /**/
        {
            $key    = $this->getKey($page, $action);
            $return = array();

            if (isset($this->rulesets[$key])) {
                $return = $this->rulesets[$key]['ruleset'];
            }

            return $return;
        }

        /**
         * Register validation ruleset.
         *
         * @octdoc  m:validate/registerRuleset
         * @param   \org\octris\core\page       $page       Instance of page ruleset applies to.
         * @param   \org\octris\core\wrapper    $wrapper    Instance of wrapped parameters to validate.
         * @param   array                       $ruleset    Validation ruleset.
         */
        public function registerRuleset(\org\octris\core\page $page, $action, \org\octris\core\wrapper $wrapper, array $ruleset)
        /**/
        {
            $key = $this->getKey($page, $action);

            $this->rulesets[$key] = array(
                'wrapper' => $wrapper,
                'ruleset' => $ruleset
            );
        }
        
        /**
         * Test a scalar value if it is of the specified type.
         *
         * @octdoc  m:validate/test
         * @param   mixed           $value              Value to test.
         * @param   string          $type               Validation type.
         * @param   array           $options            Optional options for validator.
         * @return  bool                                Returns true, if valid.
         */
        public static function test($value, $type, array $options = array())
        /**/
        {
            $return = false;
            
            if (is_scalar($value)) {
                $test   = new $type($options);
                $return = $test->validate($value);
            }
            
            return $return;
        }
        
        /**
         * Apply registered validation ruleset.
         *
         * @octdoc  m:validate/validate
         * @param   \org\octris\core\page       $page       Instance of page object of registered ruleset.
         * @param   string                      $action     Action of registered ruleset.
         */
        public function validate(\org\octris\core\page $page, $action)
        /**/
        {
            $key = $this->getKey($page, $action);
            $ret = true;

            if (isset($this->rulesets[$key])) {
                $ruleset = $this->ruleset[$key]['ruleset'];
                
                $v = new validate\schema(
                    $ruleset['schema'],
                    $ruleset['type'],
                    $ruleset['mode']
                );
                $ret = $v->validate($this->ruleset[$key]['wrapper']);
            }

            return $ret;
        }
    }
}
