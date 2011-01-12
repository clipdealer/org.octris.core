<?php

namespace org\octris\core\validate\type {
    /**
     * Validator for testing if a string contains a valid (existing) path.
     *
     * @octdoc      c:type/path
     * @copyright   copyright (c) 2010-2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class path extends \org\octris\core\validate\type 
    /**/
    {
        /**
         * Validator implementation.
         *
         * @octdoc  m:path/validate
         * @param   mixed       $value          Value to validate.
         * @return  bool                        Returns true if value is valid.
         */
        public function validate($value)
        /**/
        {
            return (is_dir($value));
        }
    }
}
