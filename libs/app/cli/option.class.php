<?php

namespace org\octris\core\app\cli {
    /**
     * Cli option handlers should inherit this class.
     *
     * @octdoc      c:cli/option
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    abstract class option
    /**/
    {
        /**
         * Handle option.
         *
         * @octdoc  m:option/prepare
         * @abstract
         */
        abstract public function prepare();
        /**/
    }
}
