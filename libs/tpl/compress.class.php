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
        /****v* compress/$files
         * SYNOPSIS
         */
        protected $files = array('js' => array(), 'css' => array());
        /*
         * FUNCTION
         *      Purpose of this property is to store all already loaded javascript/css files to silently ommit them, if
         *      they are included multiple times.
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
        protected static function exec($files, $args)
        /*
         * FUNCTION
         *      execute yuicompressor
         * INPUTS
         *      * $files (array) -- files to compress
         *      * $args (array) -- additional arguments for yuicompressor
         * OUTPUTS
         *      (string) -- name of created filename
         ****
         */
        {
            foreach ($files as &$file) {
                $file = escapeshellarg($file);
            }

            $tmp = tempnam('/tmp', 'yui');
            $cmd = sprintf(
                'cat %s | java -jar /opt/yuicompressor/yuicompressor.jar > %s',
                implode(' ', $files);
                $tmp
            );
            
            print "$tmp";
        }
        
        /****m* compress/compressCSS
         * SYNOPSIS
         */
        public static function compressCSS(array $files)
        /*
         * FUNCTION
         *      compress external css files
         * INPUTS
         *      * $files (array) -- array of files to load and compress
         * OUTPUTS
         *      (string) -- filename of compressed javascript file
         ****
         */
        {
            self::exec($files, array());
        }

        /****m* compress/compressJS
         * SYNOPSIS
         */
        public static function compressJS(array $files)
        /*
         * FUNCTION
         *      compress external JS files
         * INPUTS
         *      * $files (array) -- array of files to load and merge
         ****
         */
        {
            self::exec($files, array());
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
            $process = function($pattern, $snippet, &$files, $cb) use (&$tpl) {
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
                $this->files['js'],
                function($files) {
                    return \org\octris\core\tpl\compiler\compress::compressJS($files);
                }
            );

            // process external css
            $process(
                '<link[^>]*? href="(?!https?://)([^"]+\.css)"[^>]*/>',
                '<link rel="stylesheet" href="/styles/%s" type="text/css" />',
                $this->files['css'],
                function($files) {
                    return \org\octris\core\tpl\compiler\compress::compressCSS($files);
                }
            );
            
            return $tpl;
        }
    }
}
