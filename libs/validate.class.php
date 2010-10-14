<?php

namespace org\octris\core {
    /****c* core/validate
     * NAME
     *      validate
     * FUNCTION
     *      validation base class
     * COPYRIGHT
     *      copyright (c) 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */

    class validate {
        /****v* validate/$instance
         * SYNOPSIS
         */
        private $instance = null;
        /*
         * FUNCTION
         *      instance of validator
         ****
         */
        
        /****d* validate/T_OBJECT, T_ARRAY
         * SYNOPSIS
         */
        const T_OBJECT = 1;
        const T_ARRAY  = 2;
        /*
         * FUNCTION
         *      schema structure types
         ****
         */
         
        /****d* validate/T_ALPHA, T_ALPHANUM, T_BOOL, T_CALLBACK, T_PATH, T_PRINT, T_XDIGIT
         * SYNOPSIS
         */
        const T_ALPHA    = '\org\octris\core\validate\type\alpha';
        const T_ALPHANUM = '\org\octris\core\validate\type\alphanum';
        const T_BOOL     = '\org\octris\core\validate\type\bool';
        const T_CALLBACK = '\org\octris\core\validate\type\callback';
        const T_CHAIN    = '\org\octris\core\validate\type\chain';
        const T_PATH     = '\org\octris\core\validate\type\path';
        const T_PATTERN  = '\org\octris\core\validate\type\pattern';
        const T_PRINT    = '\org\octris\core\validate\type\print';
        const T_XDIGIT   = '\org\octris\core\validate\type\xdigit';
        const T_URL      = '\org\octris\core\validate\type\url';
        /*
         * FUNCTION
         *      available validation types
         ****
         */
        
        /****m* validate/__construct, __clone
         * SYNOPSIS
         */
        protected function __construct() {}
        protected function __clone() {}
        /*
         * FUNCTION
         *      prevent constructing multiple instances and cloning
         ****
         */
         
        /****m* validate/getInstance
         * SYNOPSIS
         */
        final public function getInstance()
        /*
         * FUNCTION
         *      return instance of validator
         * OUTPUTS
         *      (validate) -- instance of validation class
         ****
         */
        {
            if (is_null(self::$instance)) {
                self::$instance = new static();
            }
            
            return self::$instance;
        }
        
        /****m* validate/getKey
         * SYNOPSIS
         */
        public function getKey(\org\octris\core\page $page, $action)
        /*
         * FUNCTION
         *      calculate a key based on a page object and an action
         * INPUTS
         *      * $page (object) -- page object
         *      * $action (string) -- name of action
         ****
         */
        {
            return get_class($page) . '.' . $action;
        }

        /****m* validate/getRuleset
         * SYNOPSIS
         */
        public function getRuleset(\org\octris\core\page $page, $action)
        /*
         * FUNCTION
         *      return a registered validation ruleset
         * INPUTS
         *      * $page (page) -- page ruleset was registered for
         *      * $action (string) -- action ruleset was registered for
         * OUTPUTS
         *      (array) -- ruleset, array is empty, if no ruleset for specified properties was registered
         ****
         */
        {
            $key    = $this->getKey($page, $action);
            $return = array();

            if (isset($this->rulesets[$key])) {
                $return = $this->rulesets[$key]['ruleset'];
            }

            return $return;
        }

        /****m* validate/registerRuleset
         * SYNOPSIS
         */
        public function registerRuleset(\org\octris\core\page $page, $action, \org\octris\core\wrapper $wrapper, array $ruleset)
        /*
         * FUNCTION
         *      register validation ruleset
         * INPUTS
         *      * $page (page) -- page ruleset applies to
         *      * $wrapper (wrapper) -- wrapped parameters to validate
         *      * $ruleset (array) -- validation ruleset
         ****
         */
        {
            $key = $this->getKey($page, $action);

            $this->rulesets[$key] = array(
                'wrapper' => $wrapper,
                'ruleset' => $ruleset
            );
        }
        
        /****m* validate/validate
         * SYNOPSIS
         */
        public function validate(\org\octris\core\page $page, $action)
        /*
         * FUNCTION
         *      apply registered validation ruleset
         * INPUTS
         *      * $page (page) -- page object of registered ruleset
         *      * $action (string) -- action of registered ruleset
         * OUTPUTS
         *      (bool) -- returns true, if all rules validated or if no rules are defined for case
         ****
         */
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
