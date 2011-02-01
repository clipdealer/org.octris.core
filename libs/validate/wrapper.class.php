<?php

namespace org\octris\core\validate {
    /**
     * Enable validation for arrays.
     *
     * @octdoc      c:validate/wrapper
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class wrapper extends \org\octris\core\type\collection
    /**/
    {
        /**
         * Default values for a property.
         *
         * @octdoc  v:wrapper/$defaults
         * @var     array
         */
        protected $defaults = array(
            'isSet'     => true,
            'isValid'   => false,
            'isTainted' => true,
            'value'     => null,
            'tainted'   => null
        );
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:wrapper/__construct
         * @param   array       $source         Source array to use for validation.
         */
        public function __construct(array $source)
        /**/
        {
            if (($cnt = count($source)) > 0) {
                $this->keys = array_keys($source);
                $this->data = $source;
            } else {
                $this->data = array();
            }
        }

        /**
         * Validate wrapped structure.
         *
         * @octdoc  m:wrapper/validate
         * @param   array       $schema         Validation schema.
         * @return  bool                        Returns TRUE if validation succeeded
         */
        public function validate(array $schema)
        /**/
        {
            if (($valid = isset($this->data[$name]))) {
                $instance = new \org\octris\core\validate\schema($schema);
                $valid    = $instance->validate($this->data[$name]->tainted);
                    
                    if ($valid) {
                        $this->data[$name]->value = $value;
                    }
                    
                    $this->data[$name]->isTainted = false;
                    $this->data[$name]->isSet     = true;
                    $this->data[$name]->isValid   = $valid;
                } else {
                    $valid = $this->data[$name]->isValid;
                }
            }
            
            return $valid;
        }

        /**
         * Return array entry of specified offset.
         *
         * @octdoc  m:wrapper/offsetGet
         * @param   mixed       $offs           Offset to return.
         * @return  stdClass                    Value.
         */
        public function offsetGet($offs)
        /**/
        {
            if (($idx = array_search($offs, $this->keys, true)) !== false) {
                $return = $this->data[$this->keys[$idx]];
            } else {
                $return = (object)$this->defaults;
                $return->isSet = false;
                
                parent::offsetSet($offs, $return);
            }
        
            return $return;
        }

        /**
         * Overwrite an offset with an own value.
         *
         * @octdoc  m:wrapper/offsetSet
         * @param   mixed       $offs           Offset to overwrite.
         * @param   mixed       $value          Value to set.
         */
        public function offsetSet($offs, $value)
        /**/
        {
            $me = $this;
            
            $tmp = (object)$this->defaults;
            $tmp->isSet     = true;
            $tmp->isValid   = true;
            $tmp->isTainted = false;
            $tmp->tainted   = $value;
            $tmp->value     = $value;
            
            parent::offsetSet($offs, $tmp);
        }
    }
}
