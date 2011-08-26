<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\logger {
    /**
     * Base writer class.
     *
     * @octdoc      c:logger/writer
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    abstract class writer
    /**/
    {
        /**
         * Abstract method. Each writer implementation needs to implement this
         * method.
         *
         * @octdoc  m:writer/write
         * @param   array       $message        Message to send.
         * @abstract
         */
        abstract function write(array $message);
        /**/
    }
}
