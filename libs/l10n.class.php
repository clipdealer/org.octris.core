<?php

namespace org\octris\core {
    use \org\octris\core\config as config;

    /****c* core/l10n
     * NAME
     *      l10n
     * FUNCTION
     *      localisation
     * COPYRIGHT
     *      copyright (c) 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */

    class l10n {
        /****v* l10n/$instance
         * SYNOPSIS
         */
        private $instance = null;
        /*
         * FUNCTION
         *      instance of l10n class
         ****
         */
        
        /****v* l10n/$lc
         * SYNOPSIS
         */
        protected $lc = null;
        /*
         * FUNCTION
         *      locale string
         ****
         */

        /****v* l10n/$lc_mem
         * SYNOPSIS
         */
        protected $lc_mem = array();
        /*
         * FUNCTION
         *      stores language codes for restoreLocale
         ****
         */

        /****v* l10n/$cache
         * SYNOPSIS
         */
        protected $cache = array();
        /*
         * FUNCTION
         *      compiled function cache
         ****
         */

        /****v* l10n/$directory
         * SYNOPSIS
         */
        protected $directory = '';
        /*
         * FUNCTION
         *      directory of dictionary
         ****
         */

        /****m* l10n/__construct
         * SYNOPSIS
         */
        protected function __construct()
        /*
         * FUNCTION
         *      constructor
         ****
         */
        {
        }
        
        /****m* l10n/setLocalePath
         * SYNOPSIS
         */
        public function setDirectory($directory)
        /*
         * FUNCTION
         *      directory to lookup dictionary in
         * INPUTS
         *      * $directory (string) -- name of directory to set
         ****
         */
        {
            $this->directory = $directory;
        }

        /****m* l10n/getInstance
         * SYNOPSIS
         */
        public static function getInstance()
        /*
         * FUNCTION
         *      create new instance of class
         ****
         */
        {
            if (is_null(self::$instance)) {
                self::$instance = new static();
            }
            
            return self::$instance;
        }

        /****m* l10n/setLocale
         * SYNOPSIS
         */
        public function setLocale($locale) 
        /*
         * FUNCTION
         *      change locale setting
         * INPUTS
         *      * $locale (string) -- localisation string in the form of de_DE
         * OUTPUTS
         *      (string) -- returns old localisation setting
         ****
         */
        {
            $ret      = $this->lc;
            $this->lc = $locale;

            array_push($this->lc_mem, $ret);

            putenv('LANG=' . $locale);
            putenv('LC_MESSAGES=' . $locale);
            setlocale(LC_MESSAGES, $locale);

            $this->bindTextDomain('messages', $this->directory);

            return $ret;
        }

        /****m* l10n/getLocale
         * SYNOPSIS
         */
        public function getLocale() 
        /*
         * FUNCTION
         *      get current locale setting
         * OUTPUTS
         *      (string) -- current localisation in the form de_DE
         ****
         */
        {
            return $this->lc;
        }

        /****m* l10n/getLanguageCode
         * SYNOPSIS
         */
        public function getLanguageCode($code = null)
        /*
         * FUNCTION
         *      return language code from locale (eg: 'de' from 'de_DE')
         * INPUTS
         *      * $code (string) -- (optional) code to parse
         * OUTPUTS
         *      (string) -- current set language code
         ****
         */
        {
            $parts = explode('_', (is_null($code) ? $this->lc : $code));

            return strtolower($parts[0]);
        }

        /****m* l10n/getCountryCode
         * SYNOPSIS
         */
        public function getCountryCode($code = null)
        /*
         * FUNCTION
         *      return country code from locale (eg: 'DE' from 'de_DE')
         * INPUTS
         *      * $code (string) -- (optional) code to parse
         * OUTPUTS
         *      (string) -- current set country code
         ****
         */
        {
            $parts = explode('_', (is_null($code) ? $this->lc : $code));

            return strtoupper(array_pop($parts));
        }

        /****m* l10n/restoreLocale
         * SYNOPSIS
         */
        public function restoreLocale() 
        /*
         * FUNCTION
         *      one level restoring locale setting, when a setting was overwritten using setLocale.
         ****
         */
        {
            if (count($this->lc_mem) > 0) {
                $this->setLocale(array_pop($this->lc_mem));
            }
        }

        /****m* l10n/monf
         * SYNOPSIS
         */
        public function monf($money, $context = 'text/html')
        /*
         * FUNCTION
         *      money formatter
         * INPUTS
         *      * $money (mixed) -- money object or amount of money to format
         *      * $prec (int) -- optional precision - will be overwritten if formatting pattern from CLDR exists
         *      * $context (string) -- (optional) context for formatter
         * OUTPUTS
         *      (string) -- formatted money value
         ****
         */
        {
            if (!($money instanceof \org\octris\core\type\money)) {
                $money = new \org\octris\core\type\money($money);
            }

            return $money->format($context);
        }

        /****m* l10n/numf
         * SYNOPSIS
         */
        public function numf($number, $prec = null, $len = null) 
        /*
         * FUNCTION
         *      number formatter
         * INPUTS
         *      * $number (mixed) -- number object or numerical value to format
         *      * $prec (int) -- optional precision - will be overwritten if formatting pattern from CLDR exists
         * OUTPUTS
         *      (string) -- formatted number
         ****
         */
        {
            if (!($number instanceof \org\octris\core\number)) {
                $number = new \org\octris\core\number($number);
            }

            if($len != null){
                return substr($number->format(), 0, 2 + $len) ;
            } else {
                return $number->format();
            }
        }


        /****m* l10n/datef
         * SYNOPSIS
         */
        public function datef($datetime, $format = 68) 
        /*
         * FUNCTION
         *      date formatter
         * INPUTS
         *      * $data (mixed) -- date as timestamp or ISO date string
         *      * $format (int) -- optional formatting parameter. defaults to T_DATETIME_MEDIUM == 68
         * OUTPUTS
         *      (string) -- formatted date
         ****
         */
        {
            if (!($datetime instanceof \org\octris\core\datetime)) {
                $datetime = new \org\octris\core\datetime($datetime);
            }

            return $datetime->format($format);
        }

        /****m* l10n/yesno
         * SYNOPSIS
         */
        public function yesno($val, $first, $second = '')
        /*
         * FUNCTION
         *      if $val display $fists, otherwise $second
         ****
         */
        {
            return ($val ? $first : $second);
        }

        /****m* l10n/quant
         * SYNOPSIS
         */
        public function quant($val, $first, $second = null, $third = null) 
        /*
         * FUNCTION
         *      quantisation
         * INPUTS
         *      * $val (float) -- value to compare
         *      * $first (string) -- string to return if value == 1 (or second or third not set)
         *      * $second (string) -- optional string to return if value != 1
         *      * $third (string) -- optional string to return if value == 0
         * OUTPUTS
         *      (string) -- string formatted
         ****
         */
        {
            $return = $first;

            if ($val == 0 && !is_null($third)) {
                $return = $third;
            } elseif ($val != 1 && !is_null($second)) {
                $return = $second;
            }

            return sprintf($return, $val);
        }

        /****m* l10n/comify
         * SYNOPSIS
         */
        public function comify(array $list, $word, $sep = ', ')
        /*
         * FUNCTION
         *      writes out a list of values seperated by ', ' and the last one
         *      by a string eg: 'and' or 'or'.
         * INPUTS
         *      * $list (array) -- array elements to concatenate
         *      * $word (string) -- word to concatenate last item with
         *      * $sep (string) -- (optional) string to use to concatenate all list items but the last
         * NOTE
         *      inspired by: http://snippets.dzone.com/posts/show/4661
         ****
         */
        {
            $return = '';

            if (count($list) > 0) {
                $last = array_pop($list);

                $return = implode($word, array(implode($sep, $list), $last));
            }

            return $return;
        }

        /****m* l10n/gender
         * SYNOPSIS
         */
        public function gender($val, $undefined, $male, $female) 
        /*
         * FUNCTION
         *      returns text according to specified gender
         * INPUTS
         *      * $val (mixed) -- gender [mM1fFwW2nN0]
         *      * $undefined (string) -- string to return if gender not specified (gender == n, N or 0)
         *      * $male (string) -- string to return if gender is male (gender == m, M or 1)
         *      * $female (string) -- string to return if gender is female (gender == f, F, w, W or 2)
         * OUTPUTS
         *      (string) -- string according to specified gender
         ****
         */
        {
            $val = strtoupper($val);

            switch ($val) {
            case 'M':
            case '1':
                $return = $male;
                break;
            case 'F':
            case 'W':
            case '2':
                $return = $female;
                break;
            case 'N':
            case '0':
            default:
                $return = $undefined;
                break;
            }

            return $return;
        }

        /****m* l10n/bindTextDomain
         * SYNOPSIS
         */
        protected function bindTextDomain($pkg, $localedir, $codeset = 'ISO-8859-15') 
        /*
         * FUNCTION
         *      bind localisation to a specified domain (package and directory with locale texts)
         * INPUTS
         *      * $pkg (string) -- name of package (normally application name)
         *      * $localedir (string) -- base directory for localized text packages
         *      * $codeset (string) -- (optional) codeset of text domain
         * OUTPUTS
         *      (string) -- current set directory
         ****
         */
        {
            bind_textdomain_codeset($pkg, $codeset);
            $domain = bindtextdomain($pkg, $localedir);

            textdomain($pkg);

            return $domain;
        }

        /****m* l10n/gettext
         * SYNOPSIS
         */
        public function gettext() 
        /*
         * FUNCTION
         *      lookup a message for current locale dictionary - alias for _
         * INPUTS
         *      * $txt (string) -- text to lookup in dictionary
         *      * ... (mixed) -- additional optional parameters for embedded functions
         * OUTPUTS
         *      (string) -- text from dictionary or txt, if text was not found in dictionary
         ****
         */
        {
            $this->_(func_get_args());
        }

        /****m* l10n/_
         * SYNOPSIS
         */
        public function _() 
        /*
         * FUNCTION
         *      lookup a message for current locale dictionary
         * INPUTS
         *      * $txt (string) -- text to lookup in dictionary
         *      * ... (mixed) -- additional optional parameters for embedded functions
         * OUTPUTS
         *      (string) -- text from dictionary or txt, if text was not found in dictionary
         ****
         */
        {
            $args = func_get_args();

            if (is_array($args[0])) $args = $args[0];

            $txt = (string)array_shift($args);

            // get localized text from dictionary
            if ($txt !== '') {
                $txt = $this->lookup($txt);
            }

            // compile included function calls if not in cache
            if (!isset($this->cache[$txt])) {
                $this->cache[$txt] = $this->compile($txt);
            }

            return $this->cache[$txt]($this, $args);
        }

        /****m* l10n/lookup
         * SYNOPSIS
         */
        function lookup($txt)
        /*
         * FUNCTION
         *      lookup a message and return translation. this method differs from _ and gettext 
         *      in that it won't compile any inline functions.
         * INPUTS
         *      * $txt (string) -- text to lookup
         * OUTPUTS
         *      (string) -- translation for the specified string
         ****
         */
        {
            return ($txt !== '' && (($out = gettext($txt)) !== '') ? $out : $txt);
        }

        /****m* l10n/compile
         * SYNOPSIS
         */
        protected function compile($txt)
        /*
         * FUNCTION
         *      gettext message compiler
         * INPUTS
         *      * $txt (string) -- text to compile
         * OUTPUTS
         *      (callback) -- compiled code for gettext
         ****
         */
        {
            $txt     = '\'' . str_replace("'", "\'", $txt) . '\'';
            $pattern = '/\[(?:(_\d+)|(?:([^,]+))(?:,(.*?))?(?<!\\\))\]/s';

            $txt = preg_replace_callback($pattern, function($m) {
                $cmd = (isset($m[2]) ? $m[2] : '');
                $tmp = preg_split('/(?<!\\\),/', array_pop($m));
                $par = array();

                foreach ($tmp as $t) {
                    $par[] = (($t = trim($t)) && preg_match('/^_(\d+)$/', $t, $m)
                                ? '$args[' . ($m[1] - 1) . ']'
                                : '\'' . $t . '\'');
                }

                $code = ($cmd != '' 
                         ? '\' . $obj->' . $cmd . '(' . implode(',', $par) . ') . \''
                         : '\' . ' . array_shift($par) . ' . \'');

                return $code;
            }, $txt, -1, $cnt = 0);

            if ($cnt == 0) {
                return function($args) use ($txt) { return $txt; };
            } else {
                return create_function('$obj, $args', 'return ' . $txt . ';');
            }
        }

        /****m* l10n/negotiateLanguage
         * SYNOPSIS
         */
        public function negotiateLanguage($supported, $default) 
        /*
         * FUNCTION
         *      uses HTTP_ACCEPT_LANGUAGE to negotiate accepted language
         * INPUTS
         *      * $supported (array) -- array of supported languages
         *      * $default (string) -- default language to use (fallback if no accepted language matches)
         * OUTPUTS
         *      (string) -- language
         ****
         */
        {
            // generate language array
            $lc_supported = explode(',', $supported);

            $keys = explode(',', str_replace('_', '-', strtolower($supported)));
            $lc_supported = array_combine($keys, $lc_supported);

            $short = explode(',', preg_replace('/_[A-Z0-9]+/', '', $supported));
            $lc_supported = array_merge(
                $lc_supported, 
                array_flip(array_combine(array_reverse($lc_supported), $short))
            );

            // parse HTTP_ACCEPT_LANGUAGE
            // (ripped from: http://fredbird.org/lire/log/2005-09-13-getting-browser-language-settings-with-php)
            $http_accept_language = $_SERVER->import('HTTP_ACCEPT_LANGUAGE', new lima_validate_print());

            $langs = ($http_accept_language->isSet && $http_accept_language->isValid 
                      ? explode(',', $http_accept_language->value) 
                      : array());

            $lc_accepted = array();

            foreach ($langs as $lang) if (preg_match('/([a-z]{1,2})(-([a-z0-9]+))?(;q=([0-9\.]+))?/', $lang, $match)) {
                $code = $match[1];
                $morecode = (array_key_exists(3, $match) ? $match[3] : '');
                $fullcode = ($morecode ? $code . '-' . $morecode : $code);

                $coef = sprintf('%3.1f', (array_key_exists(5, $match) && $match[5] ? $match[5] : '1'));

                $key = $coef . '-' . $code;

                $lc_accepted[$key] = array(
                    'code' => $code,
                    'coef' => $coef,
                    'morecode' => $morecode,
                    'fullcode' => $fullcode
                );
            }

            krsort($lc_accepted);

            // negotiate language
            $lc_specified = $default;

            foreach ($lc_accepted as $q => $lc) {
                if (array_key_exists($lc['fullcode'], $lc_supported)) {
                    $lc_specified = $lc_supported[$lc['fullcode']];
                    break;
                } elseif (array_key_exists($lc['code'], $lc_supported)) {
                    $lc_specified = $lc_supported[$lc['code']];                    
                    break;
                }
            }

            return $lc_specified;
        }
    }
}
