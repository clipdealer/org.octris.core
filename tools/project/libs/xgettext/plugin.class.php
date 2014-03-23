<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\project\libs\xgettext {
    /**
     * Base class for xgettext plugins.
     *
     * @octdoc      c:libs/plugin
     * @copyright   copyright (c) 2014 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    abstract class plugin
    /**/
    {
        /**
         * Execute plugin.
         *
         * @octdoc  m:plugin/process
         */
        abstract public function process();
        /**/

        /**
         * Fix path names.
         *
         * @octdoc  m:plugin/fixPaths
         */
        abstract public function fixPaths();
        /**/
    }
}