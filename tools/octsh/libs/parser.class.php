<?php

namespace org\octris\core\octsh\libs {
    /**
     * Shell command parser for shell. The parser is able to parse the following commands:
     *
     * <command>[ <subcommand>[ <parameter>=<value> [...]] [...]]
     *
     * @octdoc      c:libs/parser
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class parser extends \org\octris\core\parser
    /**/
    {
        /**
         * Names of tokens. This array gets build the first time the constructor is called.
         *
         * @octdoc  v:tokenizer/$tokennames
         * @var     array
         */
        protected static $tokennames = null;
        /**/

        /**
         * Parser tokens.
         * 
         * @octdoc  d:parser/T_SYMBOL, T_PARAMETER, T_STRING, T_WHITESPACE
         */
        const T_START       = 1;
        const T_END         = 2;
        
        const T_PARAMETER   = 10;
        const T_SYMBOL      = 11;
        const T_STRING      = 12;
        const T_WHITESPACE  = 13;
        /**/

        /**
         * Regular expression patterns for parser tokens.
         *
         * @octdoc  v:parser/$tokens
         * @var     array
         */
        protected static $tokens = array(
            self::T_PARAMETER   => '[a-z][a-z0-9_]*=',
            self::T_SYMBOL      => '[a-z.0-9]+',
            self::T_STRING      => "([\"']).*?(?!\\\\)\\2",
            self::T_WHITESPACE  => '\s+'
        );
        /**/

        /**
         * Parser rules.
         *
         * @octdoc  v:parser/$rules
         * @var     array
         */
        protected static $rules = array(
            self::T_START   => array(
                self::T_END     => true,
                
                self::T_SYMBOL  => array(
                    self::T_END         => true,
                
                    self::T_WHITESPACE  => array(
                        self::T_SYMBOL      => null,
                        self::T_PARAMETER   => array(
                            self::T_SYMBOL      => null,
                            self::T_STRING      => array(
                                self::T_END         => true,
                                self::T_WHITESPACE  => null
                            )
                        )
                    )
                )
            )
        );
        /**/

        /**
         * Whether the tokenizer should ignore whitespaces.
         *
         * @octdoc  v:parser/$ignore_whitespace
         * @var     bool
         */
        protected static $ignore_whitespace = false;
        /**/

        /**
         * Error of current parsing process.
         *
         * @octdoc  v:parser/$error
         * @var     array
         */
        protected $error = array();
        /**/

        /**
         * Trigger an exception to stop parser process.
         *
         * @octdoc  m:compiler/error
         * @param   string      $type       Type of error to trigger.
         * @param   int         $cline      Line in compiler class error was triggered from.
         * @param   int         $line       Line in template the error was triggered for.
         * @param   int         $token      ID of token that triggered the error.
         * @param   mixed       $payload    Optional additional information. Either an array of expected token IDs or an additional message to output.
         */
        protected function error($type, $cline, $line, $token, $payload = NULL)
        /**/
        {
            $this->error = array(
                'type'      => $type,
                'cline'     => $cline,
                'line'      => $line,
                'token'     => $this->getTokenName($token),
                'payload'   => ''
            );

            if (is_array($payload)) {
                $this->error['payload'] = "expected: " . implode(', ', $this->getTokenNames(array_keys($payload)));
            } elseif (isset($payload)) {
                $this->error['payload'] = "message: " . $payload;
            }
         
            throw new \Exception();
        }

        /**
         * Token analyzer. The analyzer applies rulesets to tokens and checks if
         * the rules are fulfilled.
         *
         * @octdoc  m:compiler/analyze
         * @param   array       $tokens     Tokens to analyze.
         * @param   array       $blocks     Block information required by analyzer / compiler.
         * @return  bool                    Returns true if token analysis succeeded.
         */
        protected function analyze(array $tokens)
        /**/
        {
            $braces  = 0;               // brace level
            $current = null;            // current token
            
            $rule    = static::$rules;
            $stack   = array();
            
            foreach ($tokens as $current) {
                extract($current);
                
                if (!($tmp = $this->getNextRule($rule, $token, $stack))) {
                    $this->error(__FUNCTION__, __LINE__, $line, $token, $rule);
                }
                
                $rule = $tmp;
            }

            return !!($rule);
        }
        
        /**
         * Convert parsed tokens to a processable shell command.
         *
         * @octdoc  m:parser/convert
         * @param   array       $tokens         Tokens to convert.
         * @return  array                       Processable shell command.
         */
        public function convert(array $tokens)
        /**/
        {
            $parts = array();
            $refs  = array();
            $param = '';
            $idx   = 0;

            foreach ($tokens as $current) {
                extract($current);
                
                switch ($token) {
                case static::T_WHITESPACE:
                    continue;
                case static::T_PARAMETER:
                    $param = substr($value, 0, -1);
                    break;
                case static::T_SYMBOL:
                    if ($param == '') {
                        // symbol is a command
                        $refs = array();
                        
                        $parts[$idx++] = $value;
                        break;
                    } else {
                        // symbol is a parameter value
                        /** FALL THRU **/
                    }
                case static::T_STRING:
                    if ($token == static::T_STRING) {
                        $value = substr($value, 1, -1);
                    }
                
                    if (isset($refs[$param])) {
                        // parameter was already set for command -- overwrite
                        $parts[$refs[$param]][$param] = $value;
                    } else {
                        // new parameter
                        $parts[$idx] = array(
                            $param => $value
                        );
                        
                        $refs[$param] = $idx;
                        ++$idx;
                    }
                    
                    $param = '';
                    break;
                }
            }
            
            return $parts;
        }
        
        /**
         * Execute compiler toolchain for a template snippet.
         *
         * @octdoc  m:compiler/toolchain
         * @param   string      $snippet        Template snippet to process.
         * @param   int         $line           Line in template processed.
         * @param   array       $blocks         Block information required by analyzer / compiler.
         * @return  string                      Processed / compiled snippet.
         */
        protected function toolchain($snippet, $line)
        /**/
        {
            $tokens = $this->tokenize($snippet, $line);
            $return = array();

            if (count($tokens) > 0) {
                if ($this->analyze($tokens) !== false) {
                    array_shift($tokens);
                    array_pop($tokens);
                    
                    $return = $this->convert($tokens);
                }
            }
            
            return $return;
        }
        
        /**
         * Execute parser toolchain.
         *
         * @octdoc  m:parser/parse
         * @param   string      $cmd            Command to parse.
         * @param   int         $line           Line in a list of commands.
         */
        public function parse($cmd, $line)
        /**/
        {
            $this->error = array();
            
            $return = array(
                'command'   => array(),
                'error'     => false
            );

            try {
                $command = $this->toolchain($cmd, $line);

                $return['command'] = $command;
            } catch(\Exception $e) {
                $return['error'] = $this->error;
            }
            
            return $return;
        }
    }
}
