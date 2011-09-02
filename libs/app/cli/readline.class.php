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
     * Provides readline functionality either by using built-in readline
     * capabilities or by an emulation, if built-in functionality is not
     * available.
     *
     * @octdoc      c:cli/readline
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    abstract class readline
    /**/
    {
        /**
         * Class to use for new instance.
         *
         * @octdoc  v:readline/$class
         * @var     \org\octris\core\app\cli\readline
         */
        private static $class = null;
        /**/

        /**
         * Instances of readline. Each history-file has it's own instance.
         *
         * @octdoc  v:readline/$instances
         * @var     array
         */
        private static $instances = array();
        /**/

        /**
         * Whether readline history is supported.
         *
         * @octdoc  v:readline/$history
         * @var     bool
         */
        private static $history = false;
        /**/

        /**
         * Available readline drivers.
         *
         * @octdoc  v:readline/$drivers
         * @var     array
         */
        private static $drivers = array(
            '\org\octris\core\app\cli\readline\native',
            '\org\octris\core\app\cli\readline\bash',
            '\org\octris\core\app\cli\readline\emulated',
        );
        /**/

        /**
         * Number of commands allowed in history file. This is globally the same value for all readline drivers and all
         * readline instances.
         *
         * @octdoc  v:readline/$history_size
         * @var     int
         */
        protected static $history_size = 30;
        /**/

        /**
         * History file bound to instance of readline. If no file is specified, the history will not be used.
         *
         * @octdoc  v:readline/$history_file
         * @var     string
         */
        protected $history_file = '';
        /**/

        /**
         * Abstract methods
         *
         * @octdoc  m:readline/get, detect
         * @abstract
         */
        abstract public static function detect();
        abstract public function readline($prompt = '');
        /**/

        /**
         * Returns a new instance of readline. Note that no history functionality is available, if no
         * history path is provided.
         *
         * @octdoc  m:readline/getInstance
         * @param   string          $history                Optional path to a history file.
         * @return  \org\octris\core\app\cli\readlin        Instance of readline.
         */
        public final static function getInstance($history = '')
        /**/
        {
            if (!isset(self::$instances[$history])) {
                if (is_null(self::$class)) {
                    // detect and decide wich readline driver to use
                    foreach (self::$drivers as $driver) {
                        if (list(, self::$history) = $driver::detect()) {
                            self::$class = $driver;
                            break;
                        }
                    }
                }

                self::$instances[$history] = new self::$class((self::$history && !!$history ? $history : ''));
            }

            return self::$instances[$history];
        }

        /**
         * Default constructor. This method is protected to force using readline::getInstance for instance
         * creation.
         *
         * @octdoc  m:readline/__construct
         * @param   string          $history                History file to use for this readline instance.
         */
        protected function __construct($history = '')
        /**/
        {
            $this->history_file = $history;
        }
    }
}
