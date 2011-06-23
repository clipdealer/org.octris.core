<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\validate\type {
    /**
     * Validator for testing if a string is valid UTF-8.
     *
     * @octdoc      c:validate/utf8
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class utf8 extends \org\octris\core\validate\type
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
            $tmp = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            
            return ($tmp == $value);
        }
    }
}
