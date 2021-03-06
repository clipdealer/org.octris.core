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
     * Interface for write handlers.
     *
     * @octdoc      i:logger/writer_if
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    interface writer_if
    /**/
    {
        /**
         * Each writer implementation needs to implement this method.
         *
         * @octdoc  m:writer/write
         * @param   array       $message        Message to send.
         */
        public function write(array $message);
        /**/
    }
}
