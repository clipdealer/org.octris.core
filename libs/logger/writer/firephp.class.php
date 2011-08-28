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
     * Logger to send messages to FirePHP.
     *
     * @octdoc      c:writer/firephp
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class firephp extends \org\octris\core\logger\writer
    /**/
    {
        /**
         * Wildfire JSON streaming protocol header URI.
         *
         * @octdoc  v:firephp/$protocol_uri
         * @var     string
         */
        private static $protocol_uri = 'http://meta.wildfirehq.org/Protocol/JsonStream/0.2';
        /**/

        /**
         * FirePHP structure header URI.
         *
         * @octdoc  v:firephp/$structure_uri
         * @var     string
         */
        private static $structure_uri = 'http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1';
        /**/

        /**
         * Plugin header URI.
         *
         * @octdoc  v:firephp/$plugin_uri
         * @var     string
         */
        private static $plugin_uri = 'http://meta.firephp.org/Wildfire/Plugin/FirePHP/Library-FirePHPCore/0.3';
        /**/

        /**
         * Header prefix as required by Wildfire protocol.
         *
         * @octdoc  v:firephp/$prefix
         * @var     string
         */
        private static $prefix = 'X-Wf';
        /**/

        /**
         * Whether the Wildfire specific headers have been send.
         *
         * @octdoc  v:firephp/$initialized
         * @var     bool
         */
        protected static $initialized = false;
        /**/

        /**
         * Sequence number of message to send to FirePHP.
         *
         * @octdoc  v:firephp/$seq_num
         * @var     int
         */
        protected static $seq_num = 1;
        /**/

        /**
         * Maximum chunk size for JSON stream messages.
         *
         * @octdoc  v:firephp/$chunk_size
         * @var     int
         */
        protected static $chunk_size = 4096;
        /**/

        /**
         * Level names, mapping to FirePHP
         *
         * @octdoc  v:file/$level_names
         * @var     array
         */
        protected static $level_names = array(
            'ERROR',        // emergency
            'ERROR',        // alert
            'ERROR',        // critical
            'ERROR',        // error
            'WARN',         // warning
            'INFO',         // notice
            'INFO',         // info
            'LOG',          // debug
        );
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:firephp/__construct
         */
        public function __construct()
        /**/
        {
        }

        /**
         * Create header for FirePHP.
         *
         * @octdoc  m:firephp/createHeader
         * @param   array       $meta           Meta-information for header.
         * @param   string      $value          Value to set for header.
         */
        protected function createHeader(array $meta, $value)
        /**/
        {
            header(sprintf('%s-%s: %s', self::$prefix, implode('-', $meta), $value));
        }

        /**
         * Create JSON Stream for data.
         *
         * @octdoc  m:firephp/packData
         * @param   string      $type           Message type.
         * @param   string      $file           Name of file the message was issued in.
         * @param   int         $line           Number of line the message was issued in.
         * @param   string      $label          Label of message.
         * @param   array       $data           Data to wrap in a JSON stream.
         */
        public function createJsonStream($type, $file, $line, $label, array $data)
        /**/
        {
            $data = json_encode(
                array(
                    array(
                        'Type'  => $type,
                        'File'  => $file,
                        'Line'  => $line,
                        'Label' => $label
                    ),
                    $data
                )
            );

            $size = strlen($data);          // size in bytes and not in characters

            if ($size > static::$chunk_size) {
                $parts = str_split($size . $data, static::$chunk_size);

                $this->createHeader(
                    array(1, 1, 1, static::$seq_num++),
                    $size . '|' . $parts[0] . '|\\'
                );

                for ($i = 1, $cnt = count($parts); $i < ($cnt - 1); ++$i) {
                    $this->createHeader(
                        array(1, 1, 1, static::$seq_num++),
                        '|' . $parts[$i] . '|\\'
                    );
                }

                $this->createHeader(
                    array(1, 1, 1, static::$seq_num++),
                    '|' . $parts[$cnt - 1] . '|'
                );
            } else {
                $this->createHeader(
                    array(1, 1, 1, static::$seq_num++),
                    $size . '|' . $data . '|'
                );
            }
        }

        /**
         * Send logging message to a FirePHP.
         *
         * @octdoc  m:firephp/write
         * @param   array       $message        Message to send.
         */
        public function write(array $message)
        /**/
        {
            if (!static::$initialized) {
                // this is the first call to write, wildfire headers have to be send first
                $this->createHeader(array('Protocol', 1), self::$protocol_uri);
                $this->createHeader(array(1, 'Structure', 1), self::$structure_uri);
                $this->createHeader(array(1, 'Plugin', 1), self::$plugin_uri);

                static::$initialized = true;
            }

            // send message summary
            $this->createJsonStream(
                static::$level_names[$message['level']],
                $message['file'],
                $message['line'],
                $message['message'],
                array(
                    'file'      => $message['file'],
                    'line'      => $message['line'],
                    'code'      => $message['code'],
                    'message'   => $message['message'],
                    'host'      => $message['host'],
                    'time'      => sprintf(
                        '%s.%d',
                        strftime(
                            '%Y-%m-%d %H:%M:%S',
                            $message['timestamp']
                        ),
                        substr(strstr($message['timestamp'], '.' ), 1)
                    ),
                    'facility'  => $message['facility'],
                    'data'      => $message['data']
                )
            );
        }
    }
}
