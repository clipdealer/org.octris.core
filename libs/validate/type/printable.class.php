<?php

namespace org\octris\core\validate\type {
    /**
     * Validator for testing if a string contains only printable characters.
     *
     * @octdoc      c:validate/printable
     * @copyright   copyright (c) 2010-2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class printable extends \org\octris\core\validate\type
    /**/
    {
        /**
         * Validator implementation.
         *
         * @octdoc  m:print/validate
         * @param   mixed       $value          Value to validate.
         * @return  bool                        Returns true if value is valid.
         */
        public function validate($value)
        /**/
        {
            return !preg_match('/[\f\n\r\t]/', $value);
        }
    }
}
