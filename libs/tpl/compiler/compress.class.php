<?php

namespace org\octris\core\tpl\compiler {
    /****c* compiler/compress
     * NAME
     *      compress
     * FUNCTION
     *      Compress javascript and css files. This is a static class.
     * COPYRIGHT
     *      copyright (c) 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
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
            $compress = function($buffer) {
                $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
                $buffer = str_replace(array("\r\n", "\r", "\n", "\t"), '', $buffer);
                $buffer = preg_replace('/\s\s+/', ' ', $buffer);
                
                return $buffer;
            };
            
            $buffer = '';
            
            foreach ($files as $file) {
                $buffer .= $compress(file_get_contents($file));
                
            }

            if ($buffer != '') {
                file_put_contents(/* TODO: file */, $buffer);
            }

            return $content;
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
