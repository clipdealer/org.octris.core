<?php

namespace org\octris\core\validate\type {
    /**
     * Validate values containing only printable characters.
     *
     * @octdoc      c:validate/printable
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class printable extends \org\octris\core\validate\type
    /**/
    {
        /**
         * Validate value containing only printable characters.
         *
         * @octdoc  m:printable/validate
         * @param   mixed       $value      Value to validate.
         */
        public function validate($value)
        /**/
        {
            return !preg_match('/[\f\n\r\t]/', $value);
        }
    }
}
