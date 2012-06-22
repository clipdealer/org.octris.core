<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\l10n {
    /**
     * Formatting class.
     *
     * @octdoc      c:l10n/format
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class format
    /**/
    {
        /**
         * Number formatter.
         *
         * @octdoc  m:format/number
         * @param   int|float|\org\octris\core\type\number|\org\octris\core\type\money  $value          Value to format.
         * @return  string                                                                              Formatted number value.
         */
        public static function number($value)
        /**/
        {
        
        }
    
        /**
         * Money formatter.
         *
         * @octdoc  m:format/money
         * @param   int|float|\org\octris\core\type\money                               $value          Value to format.
         * @param   string                                                              $currency       Optional currency in ISO 4217.
         * @return  string                                                                              Formatted money value.
         */
        public static function money($value, $currency = null)
        /**/
        {
        
        }
    }
}
