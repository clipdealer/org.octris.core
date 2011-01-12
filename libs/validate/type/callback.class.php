<?php

namespace org\octris\core\validate\type {
    /**
     * Validator for executing user defined validation process. The validator takes a callback method that
     * will be called on validation.
     *
     * @octdoc      c:type/callback
     * @copyright   copyright (c) 2010-2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class callback extends \org\octris\core\validate\type 
    /**/
    {
        /**
         * Constructor.
         *
         * @octdoc  m:callback/__construct
         * @param   array       $options        Options required by validator.
         */
        public function __construct(array $options)
        /**/
        {
            if (!isset($options['callback']) || !is_callable($options['callback'])) {
                throw new \Exception('valid callback is required');
            }
            
            parent::__construct($options);
        }
        
        /**
         * Validator implementation.
         *
         * @octdoc  m:callback/validate
         * @param   mixed       $value          Value to validate.
         * @return  bool                        Returns true if value is valid.
         */
        public function validate($value)
        /**/
        {
            return !!$this->options['callback']($value);
        }
    }
}