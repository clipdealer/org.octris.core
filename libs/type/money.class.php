<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\type {
    /**
     * Money type.
     *
     * @octdoc      c:type/money
     * @copyright   copyright (c) 2010-2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class money extends \org\octris\core\type\number
    /**/
    {
        /**
         * Currency of money object (ISO 4217)
         *
         * @octdoc  p:money/$currency
         * @var     string
         */
        protected $currency = 'EUR';
        /**/

        /**
         * Stores callback for an optional exchange service.
         *
         * @octdoc  p:money/$xchg_service
         * @var     callback
         */
        protected static $xchg_service = null;
        /**/

        /**
         * Constructor. Note that a money object can have a currency, which is not bound to the 
         * currently set locale. If a precision is specifed, the precision will only be used for 
         * returning the money amount. For internal calculations the default precision will be
         * used.
         *
         * @octdoc  m:money/__construct
         * @param   float       $value      Optional value for money object without locale specific characters.
         * @param   string      $currency   Optional curreny (ISO 4217) to set.
         * @param   int         $precision  Optional precision to use.
         */
        public function __construct($value = 0, $currency = null, $precision = 2)
        /**/
        {
            if (!is_null($currency)) {
                $this->currency = $currency;
            }

            parent::__construct($value);
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
         * Set a callback as exchange service to allow calculating with exchange rates using
         * external services.
         *
         * @octdoc  m:money/setExchangeService
         * @param   callback        $service    Service callback.
         */
        public static function setExchangeService($service)
        /**/
        {
            if (!is_callable($service)) {
                throw new Exception('exchange service is not callable');
            } else {
                self::$xchg_service = $service;
            }
        }

        /**
         * Convert money object to an other currency using specified exchange rate.
         *
         * @octdoc  m:money/exchange
         * @param   string      $currency           Currency to convert to.
         * @param   float       $rate               Optional exchange rate. The exchange rate -- if specified -- will
         *                                          prevent the call of any set exchange service callback.
         * @param   string      $old_currency       Optional parameter which get's filled from the method with the original currency of the money object.
         * @return  \org\octris\core\type\money     Instance of current money object.
         */
        public function exchange($currency, $rate = null, &$old_currency = null)
        /**/
        {
            if (!is_null($rate)) {
                $service =& self::$xchg_service;
                $value = $service($value, $this->currency, $currency);
            } else {
                $value *= $rate;
            }
            
            $old_currency = $this->currency;
            $this->currency = $currency;
            
            return $this;
        }
        
        /**
         * Add VAT to amount of money. The new value is stored in the money object.
         *
         * @octdoc  m:money/addVat
         * @param   float       $vat                Amount of VAT to add.
         * @return  \org\octris\core\type\money     Instance of current money object.
         *
         * @todo    Think about whether it might be useful to store VAT amount in money object and
         *          whether it would be nice to have methods like "getBtto", "getNet", etc.
         */
        public function addVat($vat)
        /**/
        {
            $this->mul(1 + $vat / 100);

            return $this;
        }

        /**
         * Substract discount from amount of money. The new value is stored in the money object.
         *
         * @octdoc  f:money/subDiscount
         * @param   float       $discount           Discount to substract from amount.
         * @return  \org\octris\core\type\money     Instance of current money object.
         */
        public function subDiscount($discount)
        /**/
        {
            $this->mul(1 - $discount / 100);

            return $this;
        }
    }
    
    // set default exchange service
    money::setExchangeService(function($value, $source_currency, $target_currency) {
        return $value;
    });
}

