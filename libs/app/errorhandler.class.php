<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\app {
    /**
     * Class for wrapping standard PHP error handler into a PHP exception.
     *
     * @octdoc      c:app/errorhandler
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class errorhandler extends \Exception
    /**/
    {
        /**
         * Constructor.
         *
         * @octdoc  m:errorhandler/__construct
         * @param   string          $msg                The error message.
         * @param   int             $code               Error code.
         */
        public function __construct($msg, $code)
        /**/
        {
            parent::__construct($msg, $code);
        }

        /**
         * Helper method that is registered as error handler to catch non exceptional errors and convert them
         * to real exceptions.
         *
         * @octdoc  m:errorhandler/trigger
         * @param   int             $code               Error code.
         * @param   string          $msg                The error message.
         * @param   string          $file               The file the error war raised in.
         * @param   int             $line               The line number the error was raised in.
         * @param   array           $context            Array of active symbol table when error was raised.
         */
        public static function trigger($code, $msg, $file, $line, $context)
        /**/
        {
            $e = new static($msg, $code);
            $e->line = $line;
            $e->file = $file;

            throw $e;
        }
    }

    // register error handler for 'normal' php errors
    set_error_handler(array('\org\octris\core\app\errorhandler', 'trigger'), E_ALL);
}
