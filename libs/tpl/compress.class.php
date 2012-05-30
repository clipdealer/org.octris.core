<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\tpl {
    /**
     * Compress javascript and css files. This is a static class. This class makes use 
     * the {@link http://developer.yahoo.com/yui/compressor/ yuicompressor}.
     *
     * @octdoc      c:tpl/compress
     * @copyright   copyright (c) 2010-2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class compress
    /**/
    {
        /**
         * Default options for yuicompressor.
         *
         * @octdoc  p:compress/$defaults
         * @var     array
         */
        protected static $defaults = array('js' => array(), 'css' => array());
        /**/

        /**
         * Enable / disable YUI compressor.
         *
         * @octdoc  p:compress/$enabled
         * @var     bool
         */
        protected static $enabled = false;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:compress/__construct
         */
        public function __construct()
        /**/
        {
        }

        /**
         * Execute yuicompressor.
         *
         * @octdoc  m:compress/exec
         * @param   array       $files      Files to compress.
         * @param   string      $out        Name of path to store file in.
         * @param   string      $inp        Name of base-path to lookup source file in.
         * @param   string      $type       Type of files to compress.
         * @param   array       $options    Optional additional options for yuicompressor.
         * @return  string                  Name of created file.
         */
        protected static function exec($files, $out, $inp, $type, array $options = array())
        /**/
        {
            array_walk($files, function(&$file) use ($inp) {
                $file = escapeshellarg($inp . '/' . $file);
            });

            $tmp = tempnam('/tmp', 'oct');
            
            if (self::$enabled) {
                $options = array_merge(self::$defaults[$type], $options, array('type' => $type));
                $options = array_map(function($k, $v) {
                    return "--$k " . escapeshellarg($v);
                }, array_keys($options), array_values($options));


                $cmd = sprintf(
                    'cat %s | java -jar /opt/yuicompressor/yuicompressor.jar %s -o %s 2>&1',
                    implode(' ', $files),
                    implode(' ', $options),
                    $tmp
                );
            } else {
                $cmd = sprintf(
                    'cat %s > %s 2>&1',
                    implode(' ', $files),
                    $tmp
                );
            }

            $ret = array(); $ret_val = 0;
            exec($cmd, $ret, $ret_val);

            $md5  = md5_file($tmp);
            $name = $md5 . '.' . $type;
            rename($tmp, $out . '/' . $name);
            
            return $name;
        }
        
        /**
         * Compress external CSS files.
         *
         * @octdoc  m:compress/compressCSS
         * @param   array       $files      Array of files to load and compress.
         * @param   string      $out        Name of path to store file in.
         * @param   string      $inp        Name of base-path to lookup css file in.
         * @return  string                  Filename of compressed CSS files.
         */
        public static function compressCSS(array $files, $out, $inp)
        /**/
        {
            return self::exec($files, $out, $inp, 'css');
        }

        /**
         * Compress external Javascript files.
         *
         * @octdoc  m:compress/compressJS
         * @param   array       $files      Array of files to load and compress.
         * @param   string      $out        Name of path to store file in.
         * @param   string      $inp        Name of base-path to lookup javascript file in.
         * @return  string                  Filename of compressed Javascript files.
         */
        public static function compressJS(array $files, $out, $inp)
        /**/
        {
            return self::exec($files, $out, $inp, 'js');
        }

        /**
         * Process a template and compress all external CSS and Javascript files.
         *
         * @octdoc  m:compress/process
         * @param   string      $tpl        Template to compress
         * @param   string      $out        Array of output pathes.
         * @param   string      $inp        Array of input pathes.
         * @return  string                  Processed template.
         */
        public function process($tpl, array $out, array $inp)
        /**/
        {
            // methods purpose is to collection script/style blocks and extract all included external files. the function
            // makes sure, that files are not included multiple times
            $process = function($pattern, $snippet, $cb) use (&$tpl) {
                $files  = array();
                $offset = 0;
                
                while (preg_match("#(?:$pattern"."([\n\r\s]*))+#si", $tpl, $m_block, PREG_OFFSET_CAPTURE, $offset)) {
                    $compressed = '';

                    if (preg_match_all("#$pattern#si", $m_block[0][0], $m_tag)) {
                        // process only files, not already processed and exclude the rest
                        $diff  = array_diff($m_tag[1], $files);
                        $files = array_merge($files, $diff);

                        $compressed = sprintf($snippet, $cb($diff));
                    }

                    $compressed .= $m_block[2][0];

                    $tpl = substr_replace($tpl, $compressed, $m_block[0][1], strlen($m_block[0][0]));
                    $offset = $m_block[0][1] + strlen($compressed);
                }
            };

            // process external javascripts
            $out_js = $out['js'];
            $inp_js = $inp['js'];
            
            $process(
                '<script[^>]+src="(libsjs/\d+.js)"[^>]*></script>', 
                '<script type="text/javascript" src="/libsjs/%s"></script>',
                function($files) use ($out_js, $inp_js) {
                    return \org\octris\core\tpl\compress::compressJS($files, $out_js, $inp_js);
                }
            );

            // process external css
            $out_css = $out['css'];
            $inp_css = $inp['css'];
            
            $process(
                '<link[^>]*? href="(?!https?://)([^"]+\.css)"[^>]*/>',
                '<link rel="stylesheet" href="/styles/%s" type="text/css" />',
                function($files) use ($out_css, $inp_css) {
                    return \org\octris\core\tpl\compress::compressCSS($files, $out_css, $inp_css);
                }
            );
            
            return $tpl;
        }
    }
}
