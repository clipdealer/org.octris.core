<?php

namespace org\octris\core\app\test {
    /**
     * This wrapper is required for PHPUnit, because PHPUnit accesses
     * the PHP superglobales in the PHP way, but the OCTRiS framework
     * might have overwritten the superglobals by it's own implementation.
     * The Wrapper wraps the OCTRiS frameworks functionality and checks,
     * if access is requested from PHPUnit or from an OCTRiS framework
     * component. Access is granted the PHP way for PHPUnit requests,
     * for all other requests, it will expose the OCTRiS frameworks way.
     *
     * @octdoc      c:test/wrapper
     * @copyright   copyright (c) 2010-2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class wrapper extends \org\octris\core\validate\wrapper
    /**/
    {
        /**
         * Return array entry of specified offset.
         *
         * @octdoc  m:wrapper/offsetGet
         * @param   string              $offs           Offset.
         * @return  stdClass                            Value.
         */
        public function offsetGet($offs)
        /**/
        {
            $trace = debug_backtrace();

            foreach ($trace as $t) {
                if (strpos($t['file'], '/PHPUnit/') !== false) {
                    // call from PHPUnit
                    return $this->data[$offs]->tainted;
                }
            }
            
            return parent::offsetGet($offs);
        }
    }
}
