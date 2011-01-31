<?php

namespace org\octris\core\validate {
    use \org\octris\core\validate as validate;
    
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
         * @param   int         $mode       Optional schema validation mode.
         */
        public function __construct(array $schema, $mode = self::T_STRICT)
        /**/
        {
            $this->schema = (!isset($schema['default']) && isset($schema['type'])
                             ? array('default' => $schema)
                             : $schema);
            
            $this->mode   = $mode;
        }
 
        /**
         * Rename keys of specified array.
         *
         * @octdoc  m:schema/keyrename
         * @param   array       $ary        Array to rename keys of.
         * @param   array       $map        Map of array keys to rename array keys to.
         * @return  array                   Array with renamed keys.
         */
        protected function keyrename(array $arr, array $map)
        /**/
        {
            return (array_combine(array_map(function($v) use ($map) {
                return (is_int($v) && isset($map[$v])
                        ? $map[$v]
                        : $v);
            }, array_keys($arr)), array_values($arr)));
        }
 
        /**
         * Schema validator.
         *
         * @octdoc  m:schema/_validator
         * @param   array       $value      Value to validate.
         * @param   array       $schema     Expected schema of value.
         * @param   int         $level      Current depth in value.
         * @param   int         $max_depth  Parameter for specifying max. allowed depth of nested sub-elements.
         * @return  bool                    Returns true if validation succeeded.
         */
        protected function _validator(&$value, array $schema, $level = 0, $max_depth = 1)
        /**/
        {
            if (!($return = ($max_depth == 0 || $level <= $max_depth))) {
                // max nested depth is reached 
                return $return;
            }
        
            if (isset($schema['keyrename'])) {
                // rename keys first before continuing
                $value = $this->keyrename($value, $schema['keyrename']);
            }
            
            if ($schema['type'] == validate::T_ARRAY) {
                // array validation
                if (!($return = is_array($value))) {
                    return false;
                }
        
                $cnt = count($value);
                    
                if (isset($schema['max_items']) && $cnt > $schema['max_items']) {
                    return false;
                }
                if (isset($schema['min_items']) && $cnt < $schema['min_items']) {
                    return false;
                }
                    
                if (is_array($schema['items'])) {
                    $subschema = $schema['items'];
                } elseif (is_scalar($schema['items']) && isset($this->schema[$schema['items']])) {
                    $subschema = $this->schema[$schema['items']];
                } else {
                    // no sub-validation-schema available, continue
                    return false;
                }
                
                for ($i = 0; $i < $cnt; ++$i) {
                    $return = $this->_validator(
                        $value[$i], 
                        $subschema, 
                        $level + 1, 
                        (isset($schema['max_depth'])
                         ? $level + $schema['max_depth']
                         : $max_depth)
                    );
                    
                    if (!$return) break;
                }
            } elseif ($schema['type'] == validate::T_OBJECT) {
                // object validation
                if (!($return = is_array($value))) {
                    return false;
                }

                // validate if same properties are available in value and schema
                $schema = $schema['properties'];
                
                $cnt1 = count($schema);
                $cnt2 = count($value);
                $cnt3 = count(array_intersect_key($schema, $value));
                
                if (!($return = ($cnt1 >= $cnt3 || ($cnt1 < $cnt2 && $this->mode != self::T_STRICT)))) {
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
                        if ($this->mode == self::T_CLEANUP) {
                            unset($value[$k]);
                        }
                    
                        continue;
                    }
                
                    $return = $this->_validator($value[$k], $schema[$k], $level, $max_depth);
                    
                    if (!$return) break;
                }
            } else {
                // type validation
                if (class_exists($schema['type'])) {
                    $instance = new $schema['type'](
                        (isset($schema['options']) && is_array($schema['options'])
                                ? $schema['options']
                                : array())
                    );

                    $value  = $instance->preFilter($value);
                    $return = $instance->validate($value);
                }
            }
        
            return $return;
        }
 
        /**
         * Apply validation schema to a specified array of values.
         *
         * @octdoc  m:schema/validate
         * @param   array           $values             Array of values to validate.
         * @return  bool                                Returns true if value is valid compared to the schema configured in the validator instance.
         */
        public function validate(array &$values)
        /**/
        {
            if (!isset($this->schema['default'])) {
                throw new \Exception('no default schema specified!');
            }
            
            $return = $this->_validator(
                $values,
                $this->schema['default']
            );

            return $return;
        }
    }
}
