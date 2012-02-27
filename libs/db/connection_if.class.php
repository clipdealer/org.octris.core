<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\db {
    /**
     * Interface for database connection.
     *
     * @octdoc      i:db/connection_if
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    interface connection_if {
        /**
         * Release connection.
         *
         * @octdoc  m:connection_if/release
         */
        public function release()
        /**/
    }
}
