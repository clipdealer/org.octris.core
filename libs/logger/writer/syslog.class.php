<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\logger\writer {
    /**
     * Logger to write messages to syslog.
     *
     * @octdoc      c:writer/syslog
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class syslog extends \org\octris\core\logger\writer
    /**/
    {
        /**
         * Mapping of logger levels to syslog levels.
         *
         * @octdoc  v:syslog/$syslog_levels
         * @var     array
         */
        private static $syslog_levels = array(
            \org\octris\core\logger::T_EMERGENCY => LOG_EMERG,
            \org\octris\core\logger::T_ALERT     => LOG_ALERT,
            \org\octris\core\logger::T_CRITICAL  => LOG_CRIT,
            \org\octris\core\logger::T_ERROR     => LOG_ERR,
            \org\octris\core\logger::T_WARNING   => LOG_WARNING,
            \org\octris\core\logger::T_NOTICE    => LOG_NOTICE,
            \org\octris\core\logger::T_INFO      => LOG_INFO,
            \org\octris\core\logger::T_DEBUG     => LOG_DEBUG
        );
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:syslog/__construct
         */
        public function __construct()
        /**/
        {
            if (!(openlog('', LOG_PID | LOG_ODELAY, LOG_USER))) {
                throw new Exception(sprintf(
                    'Unable to open syslog for ident "%s"',
                    $facility
                ));
            }
        }

        /**
         * Destructor.
         *
         * @octdoc  m:syslog/__destruct
         */
        public function __destruct()
        /**/
        {
            closelog();
        }

        /**
         * Write logging message to syslog
         *
         * @octdoc  m:syslog/write
         * @param   array       $message        Message to send.
         */
        public function write(array $message)
        /**/
        {
            syslog(
                self::$syslog_levels[$message['level']],
                sprintf(
                    '%s%s(%d) **%s**',
                    ($message['facility']
                        ? $message['facility'] . ': '
                        : ''),
                    $message['file'],
                    $message['line'],
                    $message['message']
                )
            );
        }
    }
}
