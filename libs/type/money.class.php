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
         * Stores callback for an optional exchange service.
         *
         * @octdoc  v:money/$xchg_service
         * @var     callback
         */
        protected static $xchg_service = null;
        /**/

        /**
         * Constructor. Note that a money object can have a currency, which is not bound to the 
         * currently set locale.
         *
         * @octdoc  m:money/__construct
         * @param   float       $value      Optional value for money object without locale specific characters.
         * @param   string      $currency   Optional curreny (ISO 4217) to set.
         */
        public function __construct($value = 0, $currency = null)
        /**/
        {
            if (!is_null($currency)) {
                $this->currency = $currency;
            }

            $value = $this->prepare($value);

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
         * @return  string                          Old currency.
         */
        public function exchange($currency, $rate = null)
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
            
            return $old_currency;
        }
    }
    
    // set default exchange service
    money::setExchangeService(function($value, $source_currency, $target_currency) {
        return $value;
    });
}

