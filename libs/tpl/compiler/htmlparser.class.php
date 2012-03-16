<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\tpl\compiler {
    /**
     * HTML Parser for auto-escaping functionality.
     *
     * @octdoc      c:compiler/htmlparser
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class htmlparser implements \Iterator 
    /**/
    {
        /**
         * Option flags.
         *
         * @octdoc  p:htmlparser/T_DEBUG, T_IGNORE_COMMENTS
         */
        const T_DEBUG           = 1;    // for switching on debug mode
        const T_IGNORE_COMMENTS = 2;    // for ignoring commands in comments
        /**/
        
        /**
         * Parser states.
         *
         * @octdoc  p:htmlparser/T_...
         */
        const T_DATA            = 1;
        const T_COMMAND         = 2;
    
        const T_TAG_START       = 10;
        const T_TAG_END_OPEN    = 11;
        const T_TAG_END_CLOSE   = 12;
        const T_TAG_NAME        = 13;
        const T_TAG_CLOSE       = 14;
    
        const T_ATTR_START      = 20;
        const T_ATTR_END        = 21;
        const T_ATTR_COMMAND    = 22;
    
        const T_COMMENT_OPEN    = 30;
        const T_COMMENT_CLOSE   = 31;
        const T_COMMENT_COMMAND = 32;
    
        const T_CDATA_OPEN      = 40;
        const T_CDATA_CLOSE     = 41;
        const T_CDATA_COMMAND   = 42;
        /**/
    
        /**
         * Parser patterns
         *
         * @octdoc  p:htmlparser/$patterns
         * @var     array
         */
        protected static $patterns = array(
            self::T_TAG_START       => '/</',
            self::T_TAG_END_OPEN    => '/\s*>/',
            self::T_TAG_END_CLOSE   => '/\s*\/>/',
            self::T_TAG_NAME        => '/(_c_[a-f0-9]+_|(?i:[a-z]+))/',
            self::T_TAG_CLOSE       => '/\/(_c_[a-f0-9]+_|(?i:[a-z]+))>/',
            
            self::T_ATTR_START      => '/(?<=\s)(_c_[a-f0-9]+_|(?i:[a-z:_][a-z:_.-]*))=\"/',
            self::T_ATTR_END        => '/(?!\\\\)\"/',
            self::T_ATTR_COMMAND    => '/\b(_c_[a-f0-9]+_)\b/',
        
            self::T_COMMENT_OPEN    => '/<!--/',
            self::T_COMMENT_CLOSE   => '/-->/',
        
            self::T_CDATA_OPEN      => '/<!\[CDATA\[/i',
            self::T_CDATA_CLOSE     => '/\]\]>/',
        
            self::T_COMMAND         => '/\b(_c_[a-f0-9]+_)\b/',
        );
        /**/
    
        /**
         * Parser rules.
         *
         * @octdoc  p:htmlparser/$rules
         * @var     array
         */
        protected static $rules = array(
            self::T_DATA            => array(
                self::T_TAG_START,
                self::T_COMMAND,
                self::T_COMMENT_OPEN,
                self::T_CDATA_OPEN
            ),
        
            self::T_TAG_START       => array(
                self::T_TAG_NAME,
                self::T_TAG_CLOSE
            ),
        
            self::T_TAG_NAME        => array(
                self::T_TAG_END_OPEN,
                self::T_TAG_END_CLOSE,
                self::T_ATTR_START,
                self::T_COMMAND
            ),
        
            self::T_ATTR_START      => array(
                self::T_ATTR_COMMAND,
                self::T_ATTR_END
            ),
            
            self::T_ATTR_COMMAND    => array(
                self::T_ATTR_COMMAND,
                self::T_ATTR_END
            ),
            
            self::T_ATTR_END        => array(
                self::T_TAG_END_OPEN,
                self::T_TAG_END_CLOSE,
                self::T_ATTR_START,
                self::T_COMMAND
            ),
            
            self::T_COMMENT_OPEN    => array(
                self::T_COMMENT_COMMAND,
                self::T_COMMENT_CLOSE
            ),
            
            self::T_COMMENT_COMMAND => array(
                self::T_COMMENT_COMMAND,
                self::T_COMMENT_CLOSE
            ),
            
            self::T_CDATA_OPEN      => array(
                self::T_CDATA_COMMAND,
                self::T_CDATA_CLOSE
            ),
            
            self::T_CDATA_COMMAND   => array(
                self::T_CDATA_COMMAND,
                self::T_CDATA_CLOSE
            )
        );
        /**/

        /**
         * Names of parser tokens, generated by class constructor.
         *
         * @octdoc  p:htmlparser/$tokennames
         * @var     array|null
         */
        protected static $tokennames = null;
        /**/
    
        /**
         * Attributes and their relevant context information.
         *
         * @octdoc  p:htmlparser/$attributes
         * @var     array
         */
        protected static $attributes = array(
            'js' => array(
                'onload', 'onunload', 'onclick', 'ondblclick', 
                'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove', 'onmouseout', 
                'onfocus', 'onblur', 'onkeypress', 'onkeydown', 'onkeyup',
                'onsubmit', 'onreset', 'onselect', 'onchange'
            ),
            'uri' => array(
                'action', 'background', 'cite', 'classid', 'codebase', 'data',
                'href', 'longdesc', 'profile', 'src', 'usemap'
            )
        );
        /**/

        /**
         * HTML document to parse.
         *
         * @octdoc  p:htmlparser/$tpl
         * @var     string
         */
        protected $tpl;
        /**/
    
        /**
         * Current offset to start parsing from.
         *
         * @octdoc  p:htmlparser/$offset
         * @var     int
         */
        protected $offset = 0;
        /**/
    
        /**
         * Offset to start parsing from in next iteration.
         *
         * @octdoc  p:htmlparser/$next_offset
         * @var     int
         */
        protected $next_offset = 0;
        /**/

        /**
         * Current state of parser in document.
         *
         * @octdoc  p:htmlparser/$state
         * @var     int
         */
        protected $state = self::T_DATA;
        /**/

        /**
         * Whether parser is in a valid state. The parser is in a valid state, if the current parser iteration found something to work with.
         *
         * @octdoc  p:htmlparser/$valid
         * @var     bool
         */
        protected $valid = false;
        /**/

        /**
         * Current parsed content.
         *
         * @octdoc  p:htmlparser/$current
         * @var     array
         */
        protected $current = null;
        /**/

        /**
         * Stack for escaping modes.
         *
         * @octdoc  p:htmlparser/$escape
         * @var     array
         */
        protected $escape = array(\org\octris\core\tpl::T_ESC_HTML);
        /**/

        /**
         * Array for storing normalized template commands.
         *
         * @octdoc  p:htmlparser/$commands
         * @var     array
         */
        protected $commands = array();
        /**/

        /**
         * Whether debug-mode is enabled.
         *
         * @octdoc  p:htmlparser/$debug
         * @var     bool
         */
        protected $debug;
        /**/

        /**
         * Whether to ignore commands inside of HTML comments.
         *
         * @octdoc  p:htmlparser/$ignore_comments
         */
        protected $ignore_comments;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:htmlparser/__construct
         * @param   string                  $tpl                        HTML document to parse.
         * @param   int                     $flags                      Optional option flags to set.
         */
        public function __construct($tpl, $flags = 0) 
        /**/
        {
            if (is_null(self::$tokennames)) {
                $class = new \ReflectionClass($this);
                self::$tokennames = array_flip($class->getConstants());
            }
        
            $this->tpl   = $this->normalize($tpl);
            
            print $this->tpl;

            // option flags
            $this->debug           = (($flags & self::T_DEBUG) === self::T_DEBUG);
            $this->ignore_comments = (($flags & self::T_IGNORE_COMMENTS) === self::T_IGNORE_COMMENTS);
        }

        /** Implementation of methods required for Iterator interface **/

        /**
         * Set offset to 0 to parse template again.
         *
         * @octdoc  p:htmlparser/rewind
         */
        public function rewind() 
        /**/
        {
            $this->offset = 0;
        
            $this->next();
        }

        /**
         * Return current parsed command.
         *
         * @octdoc  p:htmlparser/current
         * @return  array                       Array with template command and escaping.
         */
        public function current() 
        /**/
        {
            return $this->current;
        }
        
        /**
         * Return current offset of parser.
         *
         * @octdoc  p:htmlparser/key
         * @return  int                         Offset.
         */
        public function key() 
        /**/
        {
            return $this->offset;
        }

        /**
         * This methods parses the template until a template command is reached. The template command is than evailable as iterator item.
         *
         * @octdoc  m:htmlparser/next
         */
        public function next() 
        /**/
        {
            $current = null;
        
            while (($state = $this->getNextState())) {
                // parsing in progress
                switch ($state['state']) {
                case self::T_COMMENT_COMMAND:
                    if ($this->ignore_comments) {
                        continue(2);
                    }
                    /** FALL THRU **/
                case self::T_CDATA_COMMAND:
                case self::T_ATTR_COMMAND:
                case self::T_COMMAND:
                    if (!isset($this->commands[$state['payload']])) {
                        throw new \Exception(sprintf('unknown command "%s"', $state['payload']));
                    }
                    
                    $current = array(
                        'snippet' => $this->commands[$state['payload']],
                        'escape'  => end($this->escape)
                    );
                    break(2);
                case self::T_TAG_START:
                    break;
                case self::T_TAG_NAME:
                    if (substr($state['payload'], 0, 3) == '_c_') {
                        die('template command not allowed as tag-name!');
                    } else {
                        switch (strtolower($state['payload'])) {
                        case 'script':
                            array_push($this->escape, \org\octris\core\tpl::T_ESC_JS);
                            break;
                        case 'style':
                            array_push($this->escape, \org\octris\core\tpl::T_ESC_CSS);
                            break;
                        default:
                            array_push($this->escape, \org\octris\core\tpl::T_ESC_HTML);
                            break;
                        }
                    }
                    break;
                case self::T_CDATA_CLOSE:
                case self::T_COMMENT_CLOSE:
                case self::T_TAG_END_CLOSE:
                    array_pop($this->escape);
                    /** FALL THRU **/
                case self::T_TAG_END_OPEN:
                    $this->state = self::T_DATA;
                    continue(2);
                case self::T_TAG_CLOSE:
                    if (count($this->escape) == 1) {
                        if ($this->escape[0] != \org\octris\core\tpl::T_ESC_HTML) {
                            $this->escape[0] = \org\octris\core\tpl::T_ESC_HTML;
                        }
                    } else {
                        array_pop($this->escape);
                    }
            
                    $this->state = self::T_DATA;
                    continue(2);
                case self::T_ATTR_START:
                    if (substr($state['payload'], 0, 3) == '_c_') {
                        die('template command not allowed as attribute-name!');
                    } else {
                        $name = strtolower($state['payload']);
                        
                        if (in_array($name, self::$attributes['js'])) {
                            array_push($this->escape, \org\octris\core\tpl::T_ESC_JS);
                        } elseif (in_array($name, self::$attributes['uri'])) {
                            array_push($this->escape, \org\octris\core\tpl::T_ESC_URI);
                        } else {
                            array_push($this->escape, \org\octris\core\tpl::T_ESC_ATTR);
                        }
                    }
                    break;
                case self::T_ATTR_END:
                    array_pop($this->escape);
                    break;
                }
            
                $this->state = $state['state'];
            }

            $this->current = $current;
            $this->valid   = (is_array($current));
        }

        function valid() {
            return $this->valid;
        }
    
        /** Helper methods for parser **/
    
        function getNextState() {
            $this->offset = $this->next_offset;

            if (!isset(self::$rules[$this->state])) {
                throw new \Exception(sprintf('no rule for state "%s"', self::$tokennames[$this->state]));
            }
        
            $match = false;
        
            foreach (self::$rules[$this->state] as $new_state) {
                $pattern = self::$patterns[$new_state];
            
                if (preg_match($pattern, $this->tpl, $m, PREG_OFFSET_CAPTURE, $this->offset)) {
                    if ($match === false || $m[0][1] < $match['offset']) {
                        $match = array(
                            'offset'    => $m[0][1],
                            'state'     => $new_state,
                            'token'     => self::$tokennames[$new_state],
                            'payload'   => (isset($m[1]) ? $m[1][0] : ''),
                            'escape'    => null,
                            'length'    => strlen($m[0][0])
                        );
                
                        if ($this->debug) $match['match'] = $m[0][0];
                    }
                }
            }
        
            if ($match !== false) {
                $this->next_offset = $match['offset'] + $match['length'];
            }
        
            print_r($match);
        
            return $match;
        }
    
        /**
         * Search and replace all template commands and insert them in a dictionary for simpler HTML parsing.
         *
         * @octdoc  m:htmlparser/normalize
         * @param   string                      $tpl                            HTML document to normalize.
         * @return  string                                                      Normalized HTML document.
         */
        protected function normalize($tpl)
        /**/
        {
            $tpl = preg_replace_callback('/\{\{(.*?)\}\}/', function($m) {
                $id = '_c_' . uniqid() . '_';
                $this->commands[$id] = $m[1];
            
                return $id;
            }, $tpl);
        
            return $tpl;
        }
    }
}

namespace {
    $tpl = <<<XML
<html>
    <body onload="{{value()}}">
        {{command()}}
    </body>
</html>
XML;

    $p = new \org\octris\core\tpl\compiler\htmlparser($tpl, true);
    foreach ($p as $s) {
        print "----\n";
        print_r($s);
        print "----\n";
    }
}
