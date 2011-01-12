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
        protected $currency;
        /**/

        /**
         * Constructor. Note that a money object can have a currency, which is not bound to the 
         * currently set locale.
         *
         * @octdoc  m:money/__construct
         * @param   float       $value      Optional value for money object without locale specific characters.
         * @param   string      $currency   Optional curreny (ISO 4217) to set.
         * @param   string      $lc         Optional locale to set.
         */
        public function __construct($value = 0, $currency = null, $lc = null)
        /**/
        {
            if (is_null($currency)) {
                // TODO: need to detect currency of currently set locale
                $this->currency = 'EUR';
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

        /****m* datetime/getInstance
         * SYNOPSIS
         */
        public static function getInstance($value = 0, $currency = NULL, $lc = NULL)
        /*
         * FUNCTION
         *      to provide more convenient way to acces eg. formatting methods
         * INPUTS
         *      * $value (float) -- (optional) float value for money object without locale specific characters
         *      * $currency (string) -- (optional) currency (ISO 4217) to set for object. if no currency 
         *        is specified, the currency of current set locale will be used
         *      * $lc (string) -- (optional) locale setting
         ****
         */
        {
            return new static($value, $currency, $lc);
        }

        /****m* number/__tostring
         * SYNOPSIS
         */
        public function __toString() 
        /*
         * FUNCTION
         *      method is called, when number object is casted to a string.
         * OUTPUTS
         *      (string) -- formatted output of number object
         ****
         */
        {
            return $this->format();
        }

        /****m* money/prepare
         * SYNOPSIS
         */
        private function prepare($money) 
        /*
         * FUNCTION
         *      prepare money amount or object
         * INPUTS
         *      * $money (mixed) -- an numeric money value or an money object
         * OUTPUTS
         *      (float) -- returns amount of money
         ****
         */
        {
            if (is_object($money) && $money instanceof lima_type_money) {
                // parameter is a money object
                if ($this->currency != $money->getCurrency()) {
                    throw new Exception('different currencies!');
                } else {
                    $ret = $money->getValue();
                }
            } elseif (is_numeric($money)) {
                // parameter is a valid numeric value
                $ret = $money;
            } else {
                $ret = 0;
            }

            return $ret;
        }

        /****m* money/exchange
         * SYNOPSIS
         */
        public function exchange($currency, $rate = 1)
        /*
         * FUNCTION
         *      convert money object currency to an other currency
         * INPUTS
         *      * $currency (string) -- currency to convert money object to
         *      * $rate (float) -- exchange rate
         * OUTPUTS
         *      (object) -- returns a new money object 
         * TODO
         *      * implementing a datasource to fetch valid exchange rates
         ****
         */
        {
            return new lima_money_object($this->value * $rate, $currency, $this->lc);
        }

        /****m* money/convert
         * SYNOPSIS
         */
        public function convert($currency, $rate = 1)
        /*
         * FUNCTION
         *      convert money object currency to an other currency -- alias for exchange
         * INPUTS
         *      * $currency (string) -- currency to convert money object to
         *      * $rate (float) -- exchange rate
         * OUTPUTS
         *      (object) -- returns a new money object 
         ****
         */
        {
            return $this->exchange($currency, $rate);
        }

        /****m* money/format
         * SYNOPSIS
         */
        public function format($context = 'text/html')
        /*
         * FUNCTION
         *      return locale / currency formatted object value
         * INPUTS
         *      * $context (string) -- context to format money for
         * OUTPUTS
         *      (string) -- formatted string
         ****
         */
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

        /****m* money/addVat
         * SYNOPSIS
         */
        public function addVat($vat) 
        /*
         * FUNCTION
         *      add VAT to amount of money. the new value is stored in the money object.
         * INPUTS
         *      * $vat (float) -- vat to add
         ****
         */
        {
            $this->mul(1 + $vat / 100);
        }

        /****m* money/subDiscount
         * SYNOPSIS
         */
        public function subDiscount($discount) 
        /*
         * FUNCTION
         *      subtract discount from amount of money. the new value is stored in the money object. 
         * INPUTS
         *      * $discount (float) -- discount to substract from amount
         ****
         */
        {
            $this->mul(1 - $discount / 100);
        }

        /****m* money/get
         * SYNOPSIS
         */
        public function get() 
        /*
         * FUNCTION
         *      return amount of money
         ****
         */
        {
            return $this->value;
        }

        /****m* money/set
         * SYNOPSIS
         */
        function set($value) 
        /*
         * FUNCTION
         *      set amount of money
         ****
         */
        {
            $this->value = $value;
        }

        /****m* money/add
         * SYNOPSIS
         */
        public function add($amount) 
        /*
         * FUNCTION
         *      add money
         * INPUTS
         *      * $amount (mixed) -- a numeric amount or an other money object to add
         ****
         */
        {
            $amount = $this->prepare($amount);

            $this->value += $amount;
        }

        /****m* money/sub
         * SYNOPSIS
         */
        public function sub($amount) 
        /*
         * FUNCTION
         *      substract money
         * INPUTS
         *      * $amount (mixed) -- a numeric amount or an other money object to substract
         ****
         */
        {
            $amount = $this->prepare($amount);

            $this->value -= $amount;
        }

        /****m* money/mul
         * SYNOPSIS
         */
        public function mul($amount) 
        /*
         * FUNCTION
         *      multiplicate money
         * INPUTS
         *      * $amount (mixed) -- a numeric amount or an other money object to multiplicate
         ****
         */
        {
            $amount = $this->prepare($amount);

            $this->value = $this->value * $amount;
        }

        /****m* money/div
         * SYNOPSIS
         */
        public function div($amount) 
        /*
         * FUNCTION
         *      divide money
         * INPUTS
         *      * $amount (mixed) -- a numeric amount or an other money object to divide
         ****
         */
        {
            $amount = $this->prepare($amount);

            if ($amount == 0) {
                throw new Exception('division by zero!');
            } else {
                $this->value /= $amount;
            }
        }

        /****m* money/mod
         * SYNOPSIS
         */
        public function mod($amount) 
        /*
         * FUNCTION
         *      modulo
         * INPUTS
         *      * $amount (mixed) -- a numeric amount or an other money object to modulate
         ****
         */
        {
            $amount = $this->prepare($amount);

            $this->value %= $amount;
        }

        /****m* money/getCurrency
         * SYNOPSIS
         */
        public function getCurrency()
        /*
         * FUNCTION
         *      return currency of money object
         ****
         */
        {
            return $this->currency;
        }
    }
}