<?php

namespace org\octris\core\validate\type {
    /**
     * Validator for bool values.
     *
     * @octdoc      c:type/bool
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class bool extends \org\octris\core\validate\type
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
            return (is_bool($value) || preg_match('/^(-|\+)?(0|1)$/', $value));
        }
    }
}
