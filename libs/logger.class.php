<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core {
    /**
     * Base class of logging framework.
     *
     * @octdoc      c:core/logger
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class logger
    /**/
    {
        /**
         * Error levels.
         *
         * @octdoc  d:logger/T_EMERGENCY, T_ALERT, T_CRITICAL, T_ERROR, T_WARNING, T_NOTICE, T_INFO, T_DEBUG
         */
        const T_EMERGENCY = 1;
        const T_ALERT     = 2;
        const T_CRITICAL  = 4;
        const T_ERROR     = 8;
        const T_WARNING   = 16;
        const T_NOTICE    = 32;
        const T_INFO      = 64;
        const T_DEBUG     = 128;
        /**/

        /**
         * Helper constants for making configuring writers more easy.
         *
         * @octdoc  d:logger/T_ALL
         */
        const T_ALL = 255;
        /**/

        /**
         * Configured writers.
         *
         * @octdoc  v:logger/$writers
         * @var     array
         */
        private $writers = array(
            self::T_EMERGENCY => array(),
            self::T_ALERT     => array(),
            self::T_CRITICAL  => array(),
            self::T_ERROR     => array(),
            self::T_WARNING   => array(),
            self::T_NOTICE    => array(),
            self::T_INFO      => array(),
            self::T_DEBUG     => array()
        );
        /**/

        /**
         * Level IDs in syslog format.
         *
         * @octdoc  v:logger/$level_ids
         * @var     array
         */
        private $level_ids = array(
            self::T_EMERGENCY => 0,
            self::T_ALERT     => 1,
            self::T_CRITICAL  => 2,
            self::T_ERROR     => 3,
            self::T_WARNING   => 4,
            self::T_NOTICE    => 5,
            self::T_INFO      => 6,
            self::T_DEBUG     => 7
        );
        /**/

        /**
         * Logger instance.
         *
         * @octdoc  v:logger/$instance
         * @var     \org\octris\core\logger
         */
        private static $instance = null;
        /**/

        /**
         * Facility the error was logged from. Either a value set using setValue
         * will be used or an optional string provided for 'log' method.
         *
         * @octdoc  v:logger/$facility
         * @var     string
         */
        protected $facility = '';
        /**/

        /**
         * Standard data to write to log.
         *
         * @octdoc  v:logger/$data
         * @var     array
         */
        protected $data = array();
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:logger/__construct
         */
        private function __construct()
        /**/
        {
        }

        /**
         * Implements singleton pattern, returns instance of logger.
         *
         * @octdoc  m:logger/getInstance
         * @return  \org\octris\core\logger                     Logger instance.
         */
        public static function getInstance()
        /**/
        {
            if (is_null(self::$instance)) {
                self::$instance = new static();
            }

            return self::$instance;
        }

        /**
         * Set standard value to always send to logger (expect it's overwritten)
         * in 'log' method. Note, that the special property 'facility' will be
         * used individually, see 'log' method.
         *
         * @octdoc  m:logger/setValue
         * @param   string                          $name       Name of value to set.
         * @param   mixed                           $value      Value to set.
         */
        public function setValue($name, $value)
        /**/
        {
            if ($name == 'facility') {
                $this->facility = $value;
            } else {
                $this->data[$name] = $value;
            }
        }

        /**
         * Add log writer instance.
         *
         * @octdoc  m:logger/addWriter
         * @param   int                             $level      Log level the logger belongs to.
         * @param   \org\octris\core\logger\writer  $writer     Instance of logger to add.
         */
        public function addWriter($level, \org\octris\core\logger\writer $writer)
        /**/
        {
            foreach ($this->writers as $l => &$a) {
                if (($level & $l) === $l) {
                    $a[] = $writer;
                }
            }
        }

        /**
         * Log a message to the configured writers.
         *
         * @octdoc  m:logger/log
         * @param   int         $level              Log level.
         * @param   string      $message            Short message to log.
         * @param   \Exception  $exception          Optional exception to log.
         * @param   array       $data               Optional additional fields to set. See also 'setValue' method.
         * @param   string      $facility           Optional facility name eg. application name. See also 'setValue' method.
         */
        public function log($level, $message, \Exception $exception = null, $data = array(), $facility = '')
        /**/
        {
            if (isset($this->writers[$level]) && $count($this->writers[$level]) > 0) {
                $trace = debug_backtrace();

                $message = array(
                    'host'      => gethostname(),
                    'timestamp' => microtime(true),
                    'message'   => $message,
                    'facility'  => ($facility ? $facility : $this->facility),
                    'exception' => $exception,
                    'data'      => $data,
                    'level'     => $this->level_ids[$level],
                    'line'      => 0,
                    'file'      => '',
                    'data'      => array_merge($this->data, $data)
                );

                foreach ($this->writers[$level] as $l) {
                    try {
                        $l->write($message);
                    } catch(\Exceptions $e) {
                        // make sure, that one failing logger will not prevent writing to other loggers
                    }
                }
            }
        }
    }
}
