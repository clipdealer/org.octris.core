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
         * Fail setting. Whether to fail late or early on validation. Late failing
         * is default. This means, that the validator will try to validate all 
         * fields before it returns. With 'fail early' the validator will fail and
         * return on the first invalid field.
         *
         * @octdoc  v:schema/$fail
         * @var     int
         */
        protected $fail = false;
        /**/

        /**
         * Collected errors.
         *
         * @octdoc  v:schema/$errors
         * @var     array
         */
        protected $errors = array();
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
         * Fail modes.
         * 
         * @octdoc  d:schema/T_FAIL_EARLY, T_FAIL_LATE
         */
        const T_FAIL_LATE  = 0;
        const T_FAIL_EARLY = 8;
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
            
            $mode = $mode & 7;
            
            $this->mode = ($mode == 0 
                            ? self::T_STRICT 
                            : $mode);
            $this->fail = ($mode == $mode & 8);
        }
 
        /**
         * Add validation error.
         *
         * @octdoc  m:schema/addError
         * @param   string      $msg        Error message to add.
         */
        protected function addError($msg)
        /**/
        {
            $this->errors[] = $msg;
        }

        /**
         * Add multiple validation errors.
         *
         * @octdoc  m:schema/addErrors
         * @param   array       $msg        Error messages to add.
         */
        protected function addErrors(array $msg)
        /**/
        {
            $this->errors = array_merge($this->errors, $msg);
        }

        /**
         * Return collected error messages.
         *
         * @octdoc  m:schema/getErrors
         * @return  array                   Error messages.
         */
        public function getErrors()
        /**/
        {
            return $this->errors;
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
                do {
                    if (!($return = is_array($value))) {
                        if (isset($schema['invalid'])) $this->addError($schema['invalid']);
                        break;
                    }
        
                    $cnt = count($value);
                    
                    if (!($return = (isset($schema['max_items']) && $cnt <= $schema['max_items']))) {
                        if (isset($schema['invalid'])) $this->addError($schema['invalid']);
                        break;
                    }
                    if (!($return = (isset($schema['min_items']) && $cnt >= $schema['min_items']))) {
                        if (isset($schema['invalid'])) $this->addError($schema['invalid']);
                        break;
                    }
                    
                    if (is_array($schema['items'])) {
                        $subschema = $schema['items'];
                    } elseif (is_scalar($schema['items']) && isset($this->schema[$schema['items']])) {
                        $subschema = $this->schema[$schema['items']];
                    } else {
                        // no sub-validation-schema available, continue
                        throw new \Exception("schema error -- no subschema '" . $schema['items'] . "' available");
                        $return = false;
                        break;
                    }
                
                    for ($i = 0; $i < $cnt; ++$i) {
                        if (!$this->_validator(
                            $value[$i], 
                            $subschema, 
                            $level + 1, 
                            (isset($schema['max_depth'])
                             ? $level + $schema['max_depth']
                             : $max_depth)
                        )) {
                            $return = false;

                            if ($this->fail) break;
                        }
                    }
                } while(false);
            } elseif ($schema['type'] == validate::T_OBJECT) {
                // object validation
                do {
                    if (!($return = is_array($value))) {
                        if (isset($schema['invalid'])) $this->addError($schema['invalid']);
                        break;
                    }

                    // validate if same properties are available in value and schema
                    if (!isset($schema['properties'])) {
                        throw new \Exception("schema error -- no properties available");
                    }
                    
                    $schema = $schema['properties'];
                
                    $cnt1 = count($schema);
                    $cnt2 = count($value);
                    $cnt3 = count(array_intersect_key($schema, $value));
                
                    if (!($return = ($cnt1 >= $cnt3 || ($cnt1 < $cnt2 && $this->mode != self::T_STRICT)))) {
                        if (isset($schema['invalid'])) $this->addError($schema['invalid']);
                        break;
                    }

                    if ($cnt1 > $cnt3) {
                        // iterate over missing fields and check, if they are required
                        foreach (array_diff_key($schema, $value) as $k => $v) {
                            if (isset($schema[$k]['required'])) {
                                $this->addError($schema['required']);

                                $return = false;
                                
                                if ($this->fail) break(2);
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
                
                        if (!$this->_validator($value[$k], $schema[$k], $level, $max_depth)) {
                            $return = false;
                            
                            if ($this->fail) break(2);
                        }
                    }
                } while(false);
            } elseif ($schema['type'] == validate::T_CHAIN) {
                // validation chain
                if (!isset($schema['chain'])) {
                    throw new \Exception("schema error -- no chain available");
                }
                
                $schema = $schema['chain'];
            
                foreach ($schema['chain'] as $item) {
                    if (!($return = $this->_validator($value, $item, $level, $max_depth))) {
                        break;
                    }
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
                    
                    if (!($return = $instance->validate($value)))
                        if (isset($schema['invalid'])) {
                            $this->addError($schema['invalid']);
                        }
                        
                        $this->addErrors($instance->getErrors());
                    }
                }
            }
        
            if (!$return && isset($schema['onFailure']) && is_callable($schema['onFailure'])) {
                $schema['onFailure']();
            } elseif ($return && isset($schema['onSuccess']) && is_callable($schema['onSuccess'])) {
                $schema['onSuccess']();
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
            
            $this->errors = array();
            
            $return = $this->_validator(
                $values,
                $this->schema['default']
            );

            return $return;
        }
    }
}
