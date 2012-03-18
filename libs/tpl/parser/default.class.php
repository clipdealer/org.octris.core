<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\tpl\parser {
    /**
     * Default template parser.
     *
     * @octdoc      c:parser/default
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class default extends \org\octris\core\tpl\parser
    /**/
    {
        /**
         * Pattern for parsing template commands.
         *
         * @octdoc  p:default/$pattern
         * @var     string
         */
        protected static $pattern = '/(\{\{(.*?)\}\})/s';
        /**/

        /**
         * This methods parses the template until a template command is reached. The template command is than evailable as iterator item.
         *
         * @octdoc  m:html/next
         */
        public function next() 
        /**/
        {
            $old_offset = $this->offset;

            $this->offset = $this->next_offset;

            if (($this->valid = (preg_match(self::$pattern, $this->tpl, $m, PREG_OFFSET_CAPTURE, $this->offset) > 0))) {
                $this->current = array(
                    'snippet' => (isset($m[1]) ? $m[1][0] : ''),
                    'escape'  => null,
                    'line'    => $this->getLineNumber($m[2][1]),
                    'offset'  => $m[1][1]
                );
            } else {
                $this->current = null;
            }
        }
    }
}
