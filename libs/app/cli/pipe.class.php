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
     * Pipe functionality.
     *
     * @octdoc      c:cli/pipe
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class pipe
    /**/
    {
        /**
         * Command to execute.
         *
         * @octdoc  v:pipe/$cmd
         * @var     string
         */
        protected $cmd;
        /**/

        /**
         * File to write to STDIN of command.
         *
         * @octdoc  v:pipe/$input
         * @var     string|resource|null
         */
        protected $input = null;
        /**/

        /**
         * File to write STDOUT of command to.
         *
         * @octdoc  v:pipe/$output
         * @var     string|resource|null
         */
        protected $output = null;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:pipe/__construct
         * @param   string      $cmd        Command to execute.
         */
        public function __construct($cmd)
        /**/
        {
            $this->cmd = $cmd;
        }

        /**
         * Set an file for writing to STDIN of command.
         *
         * @octdoc  m:pipe/setInput
         * @param   string|resource     $file       Input file.
         */
        public function setInput($file)
        /**/
        {
            if (is_resource($file))

            if (!is_file($file) || !is_readable($file)) {
                throw new \Exception(sprintf("'%s' is not a file or file is not readable", $file));
            }

            $this->input = $file;
        }

        /**
         * Set an output file for writing STDOUT of command to.
         *
         * @octdoc  m:pipe/setOutputFile
         * @param   string|resource     $file       Output file.
         */
        public function setOutput($file)
        /**/
        {
            if (is_resource($file)) {

            } else {
                $dir = dirname($file);

                if (!is_writable($dir)) {
                    throw new \Exception(sprintf("output directory '%s' is not writable", $dir));
                }

                $this->output = $file;
            }
        }

        /**
         * Execute command.
         *
         * @octdoc  m:pipe/exec
         * @param   string          $input      Optional input value to write to STDIN of command.
         * @return  string|bool                 Returns 'false', if command execution failed. Returns 'true',
         *                                      if command-execution succeeded and an output-file was set
         *                                      using 'setOutput' method, otherwise it returns output of command.
         */
        public function exec($input = null)
        /**/
        {
            $stdin = (is_resource($this->input)
                        ? $this->input
                        : (!is_null($this->input)
                            ? array('file', $this->input, 'r')
                            : array('pipe', 'r')));

            $stdout = (is_resource($this->output)
                        ? $this->output
                        : (!is_null($this->output)
                            ? array('file', $this->output, 'w')
                            : array('pipe', 'w')));

            $stderr = array('file', '/dev/null', 'w');

            $p_descriptors = array($stdin, $stdout, $stderr);

            $p_options = array(
                'suppress_errors' => true,
                'bypass_shell'    => true
            );

            $p_pipes = array();
            $p_cwd   = NULL;

            $proc = proc_open($this->cmd, $p_descriptors, $p_pipes, $p_cwd, $p_options);
            $out  = false;

            if (is_resource($proc)) {
                if (!is_null($input)) fwrite($p_pipes[0], $input);

                fclose($p_pipes[0]);

                $tmp = stream_get_contents($p_pipes[1]);
                fclose($p_pipes[1]);

                proc_close($proc);

                $return = (is_null($this->output)
                            ? $tmp
                            : true);
            } else {
                $return = false;
            }

            return $return;
        }
    }
}
