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
         * Return value if instance is casted to a string.
         *
         * @octdoc  m:value/__toString
         * @return  string                          Stored value as string.
         */
        public function __toString()
        /**/
        {
            return (string)$this->value;
        }
        
        /**
         * Validate value with specified validator.
         *
         * @octdoc  m:value/validate
         * @param   mixed           $validator          Validation instance or type name.
         * @param   array           $options            Optional validation options.
         * @return  bool                                Whether validation succeeded.
         */
        public function validate($validator, array $options = array())
        /**/
        {
            if ($this->isTainted) {
                if (is_scalar($validator) && class_exists($validator)) {
                    $validator = new $validator($options);
                }

                if (!($validator instanceof \org\octris\core\validate\type)) {
                    print "$validator\n";
                    throw new \Exception('invalid validator');
                }
                
                $this->isTainted = false;

                $value = $this->tainted;
                $value = $validator->preFilter($value);
            
                if (($this->isValid = $validator->validate($value))) {
                    $this->value = $value;
                }
            }
        
            return $this->isValid;
        }
    }
}
