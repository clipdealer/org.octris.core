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
     * Logger to write messages to a file.
     *
     * @octdoc      c:writer/file
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class file extends \org\octris\core\logger\writer
    /**/
    {
        /**
         * Name of file to log to.
         *
         * @octdoc  v:file/$filename
         * @var     string
         */
        protected $filename;
        /**/

        /**
         * Level names.
         *
         * @octdoc  v:file/$level_names
         * @var     array
         */
        protected $level_names = array(
            'emergency', 'alert', 'critical', 'error', 'warning',
            'notice', 'info', 'debug'
        );
        /**/


        /**
         * Constructor.
         *
         * @octdoc  m:file/__construct
         * @param   string      $filename       Name of file to log to.
         */
        public function __construct($filename)
        /**/
        {
            $this->filename = $filename;
        }

        /**
         * Write logging message to a file.
         *
         * @octdoc  m:file/write
         * @param   array       $message        Message to send.
         */
        public function write(array $message)
        /**/
        {
            if (!($fp = fopen($this->filename))) {
                // error handling
            } else {
                fwrite($fp, sprintf("message : %s\n", md5(serialize($message))));
                fwrite($fp, sprintf("          %s\n", $message['message']));
                fwrite($fp, sprintf("host    : %s\n", $message['host']));
                fwrite($fp, sprintf("time    : %s\n", strftime('%Y-%m-%d %H:%M:%S', $message['timestamp'])));
                fwrite($fp, sprintf("level   : %s\n", $this->level_names[$message['level']]));
                fwrite($fp, sprintf("facility: %s\n", $message['facility']));
                fwrite($fp, sprintf("file    : %s\n", $message['file']));
                fwrite($fp, sprintf("line    : %d\n", $message['line']));
                fwrite($fp, "data    :\n");

                $max = 0;
                array_walk($message['data'], function($v, $k) use (&$max) {
                    $max = max(strlen($k), $max);
                });

                foreach ($message['data'] as $k => $v) {
                    fwrite($fp, sprintf(
                        "  %-" . $max . "s: %s\n",
                        $k,
                        (!is_scalar($v) ? json_encode($v) : $v)
                    ));
                }

                if (!is_null($message['exception'])) {
                    fwrite($fp, "trace   :\n");
                    fwrite($fp, $message['exception']->getTraceAsString());
                }

                fwrite($fp, "\n");

                fclose($fp);
            }
        }
    }
}
