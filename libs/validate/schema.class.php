<?php

namespace org\octris\core\validate {
    /****c* validate/schema
     * NAME
     *      schema
     * FUNCTION
     *      validate by providing a validation schema
     * COPYRIGHT
     *      copyright 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     * EXAMPLE
     *      ..  source: php
     *          $v = new lima_validate_schema(array(
     *              'default' => array(
     *                  'name' => array('type' => 'alpha')
     *              )
     *          ));
     *
     *          $r = $v->validate(array(
     *              'name' => 'Harald'
     *          ));
     *
     *          $r = $v->validate(array(
     *              'name' => 'Harald'
     *          ));
     *          print (int)$r;
     *
     *          $v = new lima_validate_schema(array(
     *              'default' => array(
     *                  'names' => array('type' => 'array', 'items' => array('person'))
     *              ),
     *              'person' => array(
     *                  'name' => array('type' => 'alpha')
     *              )
     *          ));
     *          $r = $v->validate(array(
     *              'names' => array(array('name' => 'Harald'), array('name' => 'Nungki'))
     *          ));
     *          print (int)$r;
     ****
     */

    class schema {
        /****v* schema/$schema
         * SYNOPSIS
         */
        protected $schema = array();
        /*
         * FUNCTION
         *      validation schema
         ****
         */
    
        /****v* schema/$type
         * SYNOPSIS
         */
        protected $type = '';
        /*
         * FUNCTION
         *      schema base type
         ****
         */
    
        /****v* schema/$mode
         * SYNOPSIS
         */
        protected $mode;
        /*
         * FUNCTION
         *      validation mode
         ****
         */
    
        /****d* schema/T_STRICT, T_CLEANUP, T_IGNORE
         * SYNOPSIS
         */
        const T_STRICT  = 1;
        const T_CLEANUP = 2;
        const T_IGNORE  = 3;
        /*
         * FUNCTION
         *      validation modes: 
         *      *   T_STRICT:   fields not in schema will raise a validation error (default)
         *      *   T_CLEANUP:  fields not in schema will be removed
         *      *   T_IGNORE:   fields to silently ignore if not in schema
         ****
         */
    
        /****m* type/__construct
         * SYNOPSIS
         */
        function __construct(array $schema, $type, $mode = self::T_STRICT)
        /*
         * FUNCTION
         *      constructor
         * INPUTS
         *      *   $schema (array) -- (required) schema to use for validation
         *      *   $type (string) -- (required) type of base value (object/array)
         *      *   $mode (int) -- (optional) schema validation mode (default: T_STRICT)
         ****
         */
        {
            $this->schema = $schema;
            $this->type   = $type;
            $this->mode   = $mode;
        }
 
        /****m* schema/_validator
         * SYNOPSIS
         */
        protected function _validator(&$value, $type, array $schema, $level = 0, array $options = array(), $max_depth = 0)
        /*
         * FUNCTION
         *      schema validator
         * INPUTS
         *      * $value (array) -- value to validate
         *      * $type (string) -- type of value
         *      * $schema (array) -- expected schema of value
         *      * $level (int) -- (optional) current depth in value
         *      * $options (array) -- (optional) additional options
         *      * $max_depth (array) -- (optional) for specifying maximal depth of nested sub-elements
         * OUTPUTS
         *      (bool) -- true: validation successful, false: validation failed
         ****
         */
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
                        $class    = 'type\\\\' . $t;
                        $instance = new $class();
                        $return   = $instance->validate($v);
                        break;
                    }
                }

                break;
            }
        
            return $return;
        }
 
        /****m* schema/validate
         * SYNOPSIS
         */
        function validate($value)
        /*
         * FUNCTION
         *      validate an schema value
         * INPUTS
         *      * $value (mixed) -- value to validate
         * OUTPUTS
         *      (bool) -- returns true, if value is valid
         ****
         */
        {
            print_r($value);
            
            if (($return = (is_array($value) || ($value instanceof \Traversable) || ($value instanceof \ArrayAccess)))) {
                $return = $this->_validator(
                    $value, 
                    $this->type,
                    $this->schema['default']
                );
            }

            return $return;
        }
    }
}
