<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\project\app {
    /**
     * Main application class for project tools.
     *
     * @octdoc      c:app/main
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class main extends \org\octris\core\app\cli
    /**/
    {
        /**
         * Entry page to use if no other page is loaded. To be overwritten by applications' main class.
         *
         * @octdoc  v:main/$entry_page
         * @type    string
         */
        protected $entry_page = '\org\octris\core\project\app\entry';
        /**/
    }
}
