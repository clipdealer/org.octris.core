<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\app\cli {
    /**
     * Interface for readline devices.
     *
     * @octdoc      i:cli/readline_if
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    interface readline_if 
    /**/
    {
        /**
         * Detect if the device is supported by the system. It's required that this
         * method returns either 'false' if the device is not supported by the system
         * or 'true'.
         *
         * @octdoc  m:readline_if/detect
         * @return  bool                                Whether the device is supported.
         */
        public static function detect();
        /**/

        /**
         * Main readline function with optional prompt.
         *
         * @octdoc  m:readline_if/readline
         * @param   string              $prompt         Optional prompt to display.
         * @return  string                              Entered value.
         */
        public function readline($prompt = '');
        /**/
     
        /**
         * Register a completion function. If the device is unable to support input
         * completion, just leave the method body empty.
         *
         * @octdoc  m:readline/setCompletion
         * @param   callable            $callback       Callback to call for completion.
         */
        public function setCompletion(callable $callback);
        /**/
    }
}
