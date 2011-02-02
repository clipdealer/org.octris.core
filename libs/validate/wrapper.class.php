<?php

namespace org\octris\core\validate {
    require_once('wrapper/value.class.php');
    
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
                
                foreach ($source as $k => $v) {
                    $this->data[$k] = new \org\octris\core\validate\wrapper\value($v);
                }
            }
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
                $key    = $this->keys[$idx];
                $return = $this->data[$key];
            } else {
                throw new \Exception("'$offs' is not available");
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
            throw new \Exception('forbidden to set wrapped data value');
        }
        
        /**
         * Rename keys of collection but preserve the ordering of the collection.
         *
         * @octdoc  m:collection/keyrename
         * @param   array       $map            Map of origin name to new name.
         */
        public function keyrename($map)
        /**/
        {
            $this->data = parent::keyrename($map);
            $this->keys = array_keys($result);
        }

        /**
         * Set a value in wrapper.
         *
         * @octdoc  m:wrapper/set
         * @param   string      $name           Name of value to set.
         * @param   mixed       $value          Value to set.
         * @param   array       $schema         Validation schema to apply.
         * @return  bool                        Result of validation.
         */
        public function set($name, $value, array $schema)
        /**/
        {
            if (($idx = array_search($name, $this->keys, true)) === false) {
                $this->keys[] = $name;
            }
                
            $this->data[$name] = new \org\octris\core\validate\wrapper\value($value);
            
            return $this->data[$name]->validate($schema);
        }
    }
}
