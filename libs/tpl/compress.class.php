<?php

namespace org\octris\core\tpl {
    /****c* tpl/compress
     * NAME
     *      compress
     * FUNCTION
     *      Compress javascript and css files. This is a static class. This class makes use of 
     *      the yuicompressor.
     * COPYRIGHT
     *      copyright (c) 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     * REFERENCE
     *      http://developer.yahoo.com/yui/compressor/
     ****
     */

    class compress {
        /****v* compress/$path
         * SYNOPSIS
         */
        protected $path = array(
            'js'    => '/tmp',      // output path for compressed javascript
            'css'   => '/tmp'       // output path for compressed css
        );
        /*
         * FUNCTION
         *      output path for various file types
         ****
         */
        
        /****v* compress/$defaults
         * SYNOPSIS
         */
        protected static $defaults = array('js' => array(), 'css' => array());
        /*
         * FUNCTION
         *      default options for yuicompressor
         ****
         */
        
        /****m* compress/__construct
         * SYNOPSIS
         */
        public function __construct() 
        /*
         * FUNCTION
         *      constructor
         ****
         */
        {
        }

        /****m* compress/exec
         * SYNOPSIS
         */
        protected static function exec($files, $out, $type, array $options = array())
        /*
         * FUNCTION
         *      execute yuicompressor
         * INPUTS
         *      * $files (array) -- files to compress
         *      * $out (string) -- name of path to store file in
         *      * $type (string) -- type of files to compress
         *      * $options (array) -- (optional) additional options for yuicompressor
         * OUTPUTS
         *      (string) -- name of created filename
         ****
         */
        {
            array_walk($files, function(&$file) {
                $file = escapeshellarg($file);
            });
            $options = array_merge(self::$defaults[$type], $options, array('type' => $type));
            $options = array_map(function($k, $v) {
                return "--$k " . escapeshellarg($v);
            }, array_keys($options), array_values($options));
            
            $tmp = tempnam('/tmp', 'yui');
            $cmd = sprintf(
                'cat %s | java -jar /opt/yuicompressor/yuicompressor.jar %s -o %s 2>&1',
                implode(' ', $files),
                implode(' ', $options),
                $tmp
            );
            print "$cmd";
            exec($cmd, $out = array(), $ret_val = 0);
            
            print "[$ret_val]";
            
            $md5 = md5_file($tmp);
            rename($tmp, $out . '/' . $md5 . '.' . $type);
            
            return $tmp;
        }
        
        /****m* compress/compressCSS
         * SYNOPSIS
         */
        public static function compressCSS(array $files, $out)
        /*
         * FUNCTION
         *      compress external css files
         * INPUTS
         *      * $files (array) -- array of files to load and compress
         *      * $out (string) -- name of path to store file in
         * OUTPUTS
         *      (string) -- filename of compressed javascript file
         ****
         */
        {
            return self::exec($files, $out, 'css');
        }

        /****m* compress/compressJS
         * SYNOPSIS
         */
        public static function compressJS(array $files, $out)
        /*
         * FUNCTION
         *      compress external JS files
         * INPUTS
         *      * $files (array) -- array of files to load and merge
         *      * $out (string) -- name of path to store file in
         * OUTPUTS
         *      (string) -- filename of compressed javascript file
         ****
         */
        {
            return self::exec($files, $out, 'js');
        }

        /****m* compress/process
         * SYNOPSIS
         */
        public function process($tpl)
        /*
         * FUNCTION
         *      compress file
         * INPUTS
         *      * $tpl (string) -- template to compress
         * OUTPUTs
         *      (string) -- processed template
         ****
         */
        {
            // methods purpose is to collection script/style blocks and extract all included external files. the function
            // makes sure, that files are not included multiple times
            $process = function($pattern, $snippet, $cb) use (&$tpl) {
                $files = array();
                
                while (preg_match("#(?:$pattern"."([\n\r\s]*))+#si", $tpl, $m_block, PREG_OFFSET_CAPTURE)) {
                    $compressed = '';

                    if (preg_match_all("#$pattern#si", $m_block[0][0], $m_tag)) {
                        // process only files, not already processed and exclude the rest
                        $diff  = array_diff($m_tag[1], $files);
                        $files = array_merge($files, $diff);

                        $compressed = sprintf($snippet, $cb($diff));
                    }

                    $compressed .= $m_block[2][0];

                    $tpl = substr_replace($tpl, $compressed, $m_block[0][1], strlen($m_block[0][0]));
                }
            };

            // process external javascripts
            $process(
                '<script[^>]+src="(libsjs/\d+.js)"[^>]*></script>', 
                '<script type="text/javascript" src="/libsjs/%s"></script>',
                function($files) {
                    return \org\octris\core\tpl\compiler\compress::compressJS($files);
                }
            );

            // process external css
            $process(
                '<link[^>]*? href="(?!https?://)([^"]+\.css)"[^>]*/>',
                '<link rel="stylesheet" href="/styles/%s" type="text/css" />',
                function($files) {
                    return \org\octris\core\tpl\compiler\compress::compressCSS($files);
                }
            );
            
            return $tpl;
        }
    }
    
    compress::compressJS(array('/tmp/inp.js'), '/tmp');
}
