<?php

namespace org\octris\core\validate\type {
    /**
     * Validator for values containing only digits.
     *
     * @octdoc      c:type/digit
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class digit extends \org\octris\core\validate\type
    /**/
    {
        /**
         * Validator implementation.
         *
         * @octdoc  m:alpha/validate
         * @param   mixed       $value          Value to validate.
         * @return  bool                        Returns true if value is valid.
         */
        public function validate($value)
        /**/
        {
            $return = (isset($this->options['min']) 
                        ? ($value >= $this->options['min']) 
                        : true);

            $return = ($return
                        ? (isset($this->options['max'])
                            ? ($value <= $this->options['max'])
                            : true)
                        : false);

            return ($return ? preg_match('/^[0-9]+$/', $value) : false);
        }
    }
}

