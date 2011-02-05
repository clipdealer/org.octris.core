<?php

namespace org\octris\core\type {
    /**
     * Money type.
     *
     * @octdoc      c:type/money
     * @copyright   copyright (c) 2010-2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class money extends \org\octris\core\type\number
    /**/
    {
        /**
         * Currency of money object (ISO 4217)
         *
         * @octdoc  v:money/$currency
         * @var     string
         */
        protected $currency = 'EUR';
        /**/

        /**
         * Constructor. Note that a money object can have a currency, which is not bound to the 
         * currently set locale.
         *
         * @octdoc  m:money/__construct
         * @param   float       $value      Optional value for money object without locale specific characters.
         * @param   string      $currency   Optional curreny (ISO 4217) to set.
         * @param   string      $lc         Optional locale to set. -> SET LC in format!??
         */
        public function __construct($value = 0, $currency = null, $lc = null)
        /**/
        {
            if (!is_null($currency)) {
                $this->currency = $currency;
            }

            $value = $this->prepare($value);

            parent::__construct($value, $lc);

            $this->loadCLDRData(
                array(
                    'currency_sign', 'currency_format'
                ),
                $this->lc
            );

            if (strpos($this->currency_format['default'], ';') !== false) {
                $tmp = explode(';', $this->currency_format['default']);

                $this->currency_format['pos'] = $tmp[0];
                $this->currency_format['neg'] = $tmp[1];
            } else {
                $this->currency_format['pos'] = $this->currency_format['default'];
                $this->currency_format['neg'] = $this->currency_format['default'];
            }
        }

        /**
         * Set currency for money object.
         *
         * @octdoc  m:money/setCurrency
         * @param   string      $currency           Currency (ISO 4217) to set.
         */
        public function setCurrency($currency)
        /**/
        {
            $this->currency = $currency;
        }
        
        /**
         * Return currency of money object.
         *
         * @octdoc  m:money/getCurrency
         * @return  string                          Currency (ISO 4217).
         */
        public function getCurrency($currency)
        /**/
        {
            return $this->currency;
        }

        /**
         * Provide a sometimes more convenient way get a new instance of a
         * money object.
         *
         * @octdoc  m:money/getInstance
         * @param   float       $value      Optional value for money object.
         * @param   string      $currency   Optional currency (ISO 4217) to set.
         * @param   string      $lc         Optional locale string.
         */
        public function getInstance($value = 0, $currency = null, $lc = null)
        /**/
        {
            return new static($value, $currency, $lc);
        }

        /**
         * Method is called, when money object is casted to a string.
         *
         * @octdoc  m:money/__toString
         * @return  string                      Formatted output.
         */
        public function __toString()
        /**/
        {
            return $this->format();
        }

        /**
         * Convert money object to an other currency using specified exchange rate.
         *
         * @octdoc  m:money/exchange
         * @param   string      $currency           Currency to convert to.
         * @param   float       $rate               Optional exchange rate.
         * @return  \org\octris\core\type\money     New instance of money object.
         */
        public function exchange($currency, $rate = 1)
        /**/
        {
            return new static($this->value * $rate, $currency, $this->lc);
        }

        /**
         * Return locale / currency formatted string.
         *
         * @octdoc  m:momey/format
         * @param   string          $context                Context to format money for.
         * @return  string                                  Formatted string.
         */
        public function format($context = 'text/html')
        /**/
        {
            $pattern = ($this->value >= 0 ? $this->currency_format['pos'] : $this->currency_format['neg']);

            $txt = parent::format(NULL, $pattern);

            switch ($context) {
            case 'text/html':
                $txt = utf8_decode($txt);
                break;
            case 'text/plain':
                $txt = preg_replace('/[^0-9,.#+-]/', '', $txt);
                break;
            }

            return $txt;
        }

        /**
         * Return amount of money.
         *
         * @octdoc  m:money/get
         * @return  float                                   Amount of money.
         */
        public function get()
        /**/
        {
            return $this->value;
        }

        /**
         * Set amount of money.
         *
         * @octdoc  m:money/set
         * @param   float               $amount             Amount of money
         */
        public function set($value)
        /**/
        {
            $this->value = $value;
        }

        /**
         * Magic caller to implement calculation functionality.
         *
         * @octdoc  m:money/__call
         * @param   string              $func               Name of function to perform.
         * @param   array               $args               Arbitrary number of arguments of type float or money.
         */
        public function __call($func, $args)
        /**/
        {
            $args = array_map(function($v) {
                if ($v instanceof number) {
                    $v = (float)$v->get();
                } else {
                    $v = (float)$v;
                }
                
                return $v;
            });
            
            switch ($func) {
            case 'add':
                $this->value += array_sum($args);
                break;
            case 'sub':
                $this->value = array_reduce($args, function($v, $w) {
                    return $v -= $w;
                }, $this->value);
                break;
            case 'mul':
                $this->value *= array_product($args);
                break;
            case 'div':
                $this->value = array_reduce(
                    array_filter($args, function($v) {
                        return ((int)$v !== 0);
                    }, 
                    function($v, $w) {
                        return $v /= $w;
                    }, 
                    $this->value
                );
                break;
            case 'mod':
                $this->value = array_reduce($args, function($v, $w) {
                    return $v %= $w;
                }, $this->value);
                break;
            }
        }

        /**
         * Return currency of money object in ISO format.
         *
         * @octdoc  m:money/getCurrency
         * @return  string                              Currency.   
         */
        public function getCurrency()
        /**/
        {
            return $this->currency;
        }
    }
}