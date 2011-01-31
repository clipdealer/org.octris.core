<?php

namespace org\octris\core\validate {
    /**
     * Validate by providing a validation schema.
     *
     * @octdoc      c:validate/schema
     * @copyright   copyright (c) 2010-2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class schema
    /**/
    {
        /**
         * Validation schema.
         *
         * @octdoc  v:schema/$schema
         * @var     array
         */
        protected $schema = array();
        /**/

        /**
         * Schema base type.
         *
         * @octdoc  v:schema/$type
         * @var     string
         */
        protected $type = '';
        /**/

        /**
         * Validation mode.
         *
         * @octdoc  v:schema/$mode
         * @var     int
         */
        protected $mode;
        /**/

        /**
         * Available validation modes:
         *
         * - T_STRICT:  fields not in schema will raise a validation error (default)
         * - T_CLEANUP: fields not in schema will be removed
         * - T_IGNORE:  fields not in schema will be silently ignored
         *
         * @octdoc  d:schema/T_STRING, T_CLEANUP, T_IGNORE
         */
        const T_STRICT  = 1;
        const T_CLEANUP = 2;
        const T_IGNORE  = 3;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:schema/__construct
         * @param   array       $schema     Schema to use for validation.
         * @param   string      $type       Type of base value ('object' or 'array')
         * @param   int         $mode       Optional schema validation mode.
         */
        public function __construct(array $schema, $type, $mode = self::T_STRICT)
        /**/
        {
            $this->schema = $schema;
            $this->type   = $type;
            $this->mode   = $mode;
        }
 
        /**
         * Schema validator.
         *
         * @octdoc  m:schema/_validator
         * @param   array       $value      Value to validate.
         * @param   string      $type       Type of value.
         * @param   array       $schema     Expected schema of value.
         * @param   int         $level      Current depth in value.
         * @param   array       $options    Additional options.
         * @param   int         $max_depth  Parameter for specifying max. allowed depth of nested sub-elements.
         * @return  bool                    Returns true if validation succeeded.
         */
        protected function _validator(&$value, $type, array $schema, $level = 0, array $options = array(), $max_depth = 0)
        /**/
        {
            $return = ($max_depth == 0 || $level <= $max_depth);
        
            if (!$return) return $return;
        
            switch (strtolower($type)) {
            case 'array':
                $cnt = count($value);
        
                if (isset($options['max']) && $cnt > $options['max']) {
                    $return = false;
                    break;
                }
                if (isset($options['min']) && $cnt < $options['min']) {
                    $return = false;
                    break;
                }
        
                for ($i = 0; $i < $cnt; ++$i) {
                    if (!($return = $this->_validator($value[$i], 'object', $schema, $level, array(), $max_depth))) {
                        break;
                    }
                }
                break;
            case 'object':
                // validate if same properties are available in value and schema
                print_r(array($schema, $value));
                die;

                $cnt1 = count($schema);
                $cnt2 = count($value);
                $cnt3 = count(array_intersect_key($schema, $value));
                
                if (!($return = ($cnt1 >= $cnt3 || ($cnt1 < $cnt2 && $this->options['mode'] != self::T_STRICT)))) {
                    break;
                }

                if ($cnt1 > $cnt3) {
                    // iterate over missing fields and check, if they are required
                    foreach (array_diff_key($schema, $value) as $k => $v) {
                        if (!($return = (!isset($schema[$k]['required']) || !$schema[$k]['required']))) {
                            return $return;
                        }
                    }
                }

                // validate each property
                foreach ($value as $k => &$v) {
                    if (!isset($schema[$k])) {
                        // unknown field
                        if ($this->options['mode'] == self::T_CLEANUP) {
                            unset($value[$k]);
                        }
                    
                        continue;
                    }
                
                    $t = strtolower($schema[$k]['type']);

                    switch ($t) {
                    case 'object':
                        // v needs to be an array to be valid
                        if (!($return = is_array($v))) {
                            break;
                        }
                    
                        $return = $this->_validator($v, 'object', $schema[$k]['properties'], $level);
                        break;
                    case 'array':
                        // v needs to be an array to be valid
                        if (!($return = is_array($v))) {
                            break;
                        }
                    
                        // iterate over each possible allowed schema of sub-array, break if one is valid
                        foreach ($schema[$k]['items'] as $s) {
                            if (!($return = isset($this->options['schema'][$s]))) {
                                // schema not available
                                continue;
                            }
                        
                            $o = (isset($schema[$k]['options'])
                                  ? $schema[$k]['options']
                                  : array());

                            $return = $this->_validator(
                                $v, 
                                'array', 
                                $this->options['schema'][$s], 
                                $level + 1, 
                                $o,
                                (isset($o['max_depth'])
                                 ? $level + $o['max_depth']
                                 : $max_depth)
                            );
                        
                            if ($return) break;
                        }
                        break;
                    default:
                        // type validation
                        $o = (isset($schema[$k]['options']) && is_array($schema[$k]['options'])
                                ? $schema[$k]['options']
                                : array());
                        
                        $instance = new $t($o);
                        $v        = $instance->preFilter($v);
                        $return   = $instance->validate($v);
                        break;
                    }
                }

                break;
            }
        
            return $return;
        }
 
        /**
         * Apply validation schema to specified wrapped values.
         *
         * @octdoc  m:schema/validate
         * @param   \org\octris\core\validate\wrapper   $wrapper    Wrapped values to validate.
         * @return  bool                                            Returns true if value is valid compared to the schema configured in the validator instance.
         */
        public function validate(\org\octris\core\validate\wrapper $wrapper)
        /**/
        {
            $return = $this->_validator(
                $wrapper, 
                $this->type,
                $this->schema['default']
            );

            return $return;
        }
    }
}
