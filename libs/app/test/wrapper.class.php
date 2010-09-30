<?php

namespace org\octris\core\app\test {
    /****c* test/wrapper
     * NAME
     *      wrapper
     * FUNCTION
     *      This wrapper is required for PHPUnit, because PHPUnit accesses
     *      the PHP superglobales in the PHP way, but the OCTRiS framework
     *      might have overwritten the superglobals by it's own implementation.
     *      The Wrapper wraps the OCTRiS frameworks functionality and checks,
     *      if access is requested from PHPUnit or from an OCTRiS framework
     *      component. Access is granted the PHP way for PHPUnit requests,
     *      for all other requests, it will expose the OCTRiS frameworks way.
     * COPYRIGHT
     *      copyright (c) 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */

    class wrapper extends \org\octris\core\validate\wrapper {
        /****m* wrapper/offsetGet
         * SYNOPSIS
         */
        public function offsetGet($offs)
        /*
         * FUNCTION
         *      return array entry of specified offset
         * INPUTS
         *      * $offs (string) -- offset
         * OUTPUTS
         *      (stdClass) -- value
         ****
         */
        {
            $trace = debug_backtrace();
            
            if (strpos($trace[0]['file'], '/PHPUnit/') !== false) {
                // call from PHPUnit
                return $this->data[$offs]->tainted;
            } else {
                return parent::offsetGet($offs);
            }
        }
    }
}