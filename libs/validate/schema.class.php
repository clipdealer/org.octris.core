<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
         * @octdoc  p:schema/$schema
         * @type    array
         */
        protected $schema = array();
        /**/

        /**
         * Validation mode.
         *
         * @octdoc  p:schema/$mode
         * @type    int
         */
        protected $mode;
        /**/

        /**
         * Fail setting. Whether to fail late or early on validation. Late failing
         * is default. This means, that the validator will try to validate all 
         * fields before it returns. With 'fail early' the validator will fail and
         * return on the first invalid field.
         *
         * @octdoc  p:schema/$fail
         * @type    int
         */
        protected $fail = false;
        /**/

        /**
         * Collected errors.
         *
         * @octdoc  p:schema/$errors
         * @type    array
         */
        protected $errors = array();
        /**/

        /**
         * Sanitzed data.
         *
         * @octdoc  p:schema/$data
         * @type    array
         */
        protected $data = array();
        /**/

        /**
         * Whether validation succeeded.
         *
         * @octdoc  p:schema/$is_valid
         * @type    bool
         */
        protected $is_valid = false;
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
        public function __construct(array $schema, $mode = self::T_IGNORE)
        /**/
        {
            $this->schema = (!isset($schema['default']) && isset($schema['type'])
                             ? array('default' => $schema)
                             : $schema);
            
            $mode = $mode & 7;
            
            $this->mode = ($mode == 0 
                            ? self::T_STRICT 
                            : $mode);
            $this->fail = ($mode == ($mode & 8));
        }
 
        /**
         * Add validation error.
         *
         * @octdoc  m:schema/addError
         * @param   string      $msg        Error message to add.
         */
        public function addError($msg)
        /**/
        {
            $this->errors[] = $msg;
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
         * Return sanitized data.
         *
         * @octdoc  m:schema/getData
         * @return  array                   Data.
         */
        public function getData()
        /**/
        {
            return $this->data;
        }
 
        /**
         * Returns whether validation succeeded.
         *
         * @octdoc  m:schema/isValid
         * @return  bool                    Returns true, if validation succeeded.
         */
        public function isValid()
        /**/
        {
            return $this->is_valid;
        }

        /**
         * Schema validator.
         *
         * @octdoc  m:schema/_validator
         * @param   mixed       $data       Value to validate.
         * @param   array       $schema     Expected schema of value.
         * @param   int         $level      Current depth in value.
         * @param   int         $max_depth  Parameter for specifying max. allowed depth of nested sub-elements.
         * @return  bool                    Returns true if validation succeeded.
         */
        protected function _validator($data, array $schema, $level = 0, $max_depth = 0)
        /**/
        {
            if (!($return = ($max_depth == 0 || $level <= $max_depth))) {
                // max nested depth is reached 
                return $return;
            }
        
            if (isset($schema['keyrename'])) {
                // rename keys first before continuing
                $map =& $schema['keyrename'];
                $data = array_combine(array_map(function($v) use ($map) {
                    return (isset($map[$v])
                            ? $map[$v]
                            : $v);
                }, array_keys($data)), array_values($data));
            }
        
            if (isset($schema['preprocess']) && is_callable($schema['preprocess'])) {
                // there's a data preprocessor configured
                $data = $schema['preprocess']($data);
            }
            
            if ($schema['type'] == validate::T_ARRAY) {
                // array validation
                do {
                    if (!is_array($data)) {
                        if (!($return = !isset($schema['required']))) {
                            $this->addError($schema['required']);
                        }
                        
                        break;
                    }
                    
                    $cnt = count($data);
                    
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
                        list($return, $data[$i]) = $this->_validator(
                            $data[$i], 
                            $subschema, 
                            $level + 1, 
                            (isset($schema['max_depth'])
                             ? $level + $schema['max_depth']
                             : $max_depth)
                        );
                        
                        if (!$return && $this->fail) break;
                    }
                } while(false);
            } elseif ($schema['type'] == validate::T_OBJECT) {
                // object validation
                do {
                    if (!is_array($data)) {
                        if (!($return = !isset($schema['required']))) {
                            $this->addError($schema['required']);
                        }
                        
                        break;
                    }
                    
                    // validate if same properties are available in value and schema
                    if (!isset($schema['properties'])) {
                        throw new \Exception("schema error -- no properties available");
                    }
                    
                    $schema = $schema['properties'];
                
                    $cnt1 = count($schema);
                    $cnt2 = count($data);
                    $cnt3 = count(array_intersect_key($schema, $data));
                
                    if (!($return = ($cnt1 >= $cnt3 || ($cnt1 < $cnt2 && $this->mode != self::T_STRICT)))) {
                        if (isset($schema['invalid'])) $this->addError($schema['invalid']);
                        break;
                    }

                    if ($cnt1 > $cnt3) {
                        // iterate over missing fields and check, if they are required
                        foreach (array_diff_key($schema, $data) as $k => $v) {
                            if (isset($schema[$k]['required'])) {
                                $this->addError($schema[$k]['required']);

                                $return = false;
                                
                                if ($this->fail) break(2);
                            }
                        }
                    }

                    // validate each property
                    foreach ($data as $k => &$v) {
                        if (!isset($schema[$k])) {
                            // unknown field
                            if ($this->mode == self::T_CLEANUP) {
                                unset($data[$k]);
                            }
                    
                            continue;
                        }
                
                        list($return, $data[$k]) = $this->_validator($data[$k], $schema[$k], $level, $max_depth);
                        
                        if (!$return && $this->fail) break(2);
                    }
                } while(false);
            } elseif ($schema['type'] == validate::T_CHAIN) {
                // validation chain
                if (!isset($schema['chain'])) {
                    throw new \Exception("schema error -- no chain available");
                }
                
                foreach ($schema['chain'] as $item) {
                    list($return, $data) = $this->_validator($data, $item, $level, $max_depth);
                    
                    if (!$return && $this->fail) break;
                }
            } elseif ($schema['type'] == validate::T_CALLBACK) {
                // validating using callback
                if (!isset($schema['callback']) || !is_callable($schema['callback'])) {
                    throw new \Exception("schema error -- no valid callback available");
                }
                
                if (!($return = $schema['callback']($data, $this)) && isset($schema['invalid'])) {
                    $this->addError($schema['invalid']);
                }
            } else {
                // type validation
                $validator = $schema['type'];
                
                if (is_scalar($validator) && class_exists($validator) && is_subclass_of($validator, '\org\octris\core\validate\type')) {
                    $validator = new $validator(
                        (isset($schema['options']) && is_array($schema['options'])
                            ? $schema['options']
                            : array())
                    );
                }
                
                if (!($validator instanceof \org\octris\core\validate\type)) {
                    throw new \Exception("'$type' is not a validation type");
                }

                $data   = $validator->preFilter($data);
                
                if (!($return = \org\octris\core\type\string::isUtf8($data))) {
                    // no valid UTF-8 string, issue a notice
                    trigger_error('not a valid UTF-8 string', E_NOTICE);
                } else {
                    $return = $validator->validate($data);
                }

                if (!$return && isset($schema['invalid'])) {
                    $this->addError($schema['invalid']);
                }
            }
        
            if (!$return && isset($schema['onFailure']) && is_callable($schema['onFailure'])) {
                $schema['onFailure']();
            } elseif ($return && isset($schema['onSuccess']) && is_callable($schema['onSuccess'])) {
                $schema['onSuccess']();
            }

            return array($return, $data);
        }
 
        /**
         * Apply validation schema to a specified array of values.
         *
         * @octdoc  m:schema/validate
         * @param   mixed           $data               Data to validate.
         * @return  bool                                Returns true if value is valid compared to the schema configured in the validator instance.
         */
        public function validate($data)
        /**/
        {
            if (!isset($this->schema['default'])) {
                throw new \Exception('no default schema specified!');
            }
            
            $this->errors = array();
            
            list($return, $data) = $this->_validator(
                $data,
                $this->schema['default']
            );
            
            $this->data     = $data;
            $this->is_valid = $return;
            
            return ($return !== false ? $data : $return);
        }
    }
}
