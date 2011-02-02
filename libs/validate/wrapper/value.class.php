<?php

namespace org\octris\core\validate\wrapper {
    /**
     * Wrapped value.
     *
     * @octdoc      c:wrapper/value
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class value
    /**/
    {
        /**
         * Whether value is tainted.
         *
         * @octdoc  v:value/$isTainted
         * @var     bool
         */
        protected $isTainted = true;
        /**/
        
        /**
         * Whether value marked as valid.
         *
         * @octdoc  v:value/$isValid
         * @var     bool
         */
        protected $isValid = false;
        /**/
        
        /**
         * Stored value.
         *
         * @octdoc  v:value/$value
         * @var     mixed
         */
        protected $value = null;
        /**/
        
        /**
         * Tainted value.
         *
         * @octdoc  v:value/$tainted
         * @var     mixed
         */
        protected $tainted = null;
        /**/
        
        /**
         * Constructor
         *
         * @octdoc  m:value/__construct
         * @param   mixed           $value          Value to set.
         */
        public function __construct($value)
        /**/
        {
            $this->tainted = $value;
        }
        
        /**
         * Magic getter to implement read-only properties.
         *
         * @octdoc  m:value/__get
         * @param   string          $name           Name of property to return.
         * @return  mixed                           Stored value of property.
         */
        public function __get($name)
        /**/
        {
            return $this->{$name};
        }
        
        /**
         * Validate value with specified schema.
         *
         * @octdoc  m:value/validate
         * @param   array           $schema         Validation schema.
         * @return  bool                            Whether validation succeeded.
         */
        public function validate(array $schema)
        /**/
        {
            if ($this->isTainted) {
                if (!isset($schema['default'])) {
                    $schema = array('default' => $schema);
                }
            
                $value = $this->tainted;
            
                $validator = new \org\octris\core\validate\schema($schema);

                $this->isValid   = $validator->validate($value);
                $this->isTainted = false;
            
                $this->value = $value;
            }
        
            return $this->isValid;
        }
    }
}