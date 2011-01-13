<?php

namespace org\octris\core {
    use \org\octris\core\config as config;

    /**
     * Localisation library.
     *
     * @octdoc      c:core/l10n
     * @copyright   copyright (c) 2010-2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class l10n
    /**/
    {
        /**
         * Instance of l10n class for singleton pattern.
         *
         * @octdoc  v:l10n/$instance
         * @var     \org\octris\core\l10n
         */
        private $instance = null;
        /**/

        /**
         * Locale string.
         *
         * @octdoc  v:l10n/$lc
         * @var     string
         */
        protected $lc = null;
        /**/

        /**
         * Stores language codes for restoreLocale
         *
         * @octdoc  v:l10n/$lc_mem
         * @var     array
         */
        protected $lc_mem = array();
        /**/

        /**
         * Gettext compiler cache.
         *
         * @octdoc  v:l10n/$cache
         * @var     array
         */
        protected $cache = array();
        /**/

        /**
         * Directory of dictionary
         *
         * @octdoc  v:l10n/$directory
         * @var     string
         */
        protected $directory = '';
        /**/

        /**
         * Protected constructor and magic clone method. L10n is a singleton.
         *
         * @octdoc  m:l10n/__construct
         */
        protected function __construct() {}
        protected function __clone() {}
        /**/

        /**
         * Directory to lookup dictionary in.
         *
         * @octdoc  m:l10n/setDirectory
         * @param   string      $directory      Name of directory to set for looking up dictionary.
         */
        public function setDirectory($directory)
        /**/
        {
            $this->directory = $directory;
        }

        /**
         * Return instance of localization class.
         *
         * @octdoc  m:l10n/getInstance
         * @return  \org\octris\core\l10n       Instance of localization class.
         */
        public static function getInstance()
        /**/
        {
            if (is_null(self::$instance)) {
                self::$instance = new static();
            }
            
            return self::$instance;
        }

        /**
         * Change locale setting for application.
         *
         * @octdoc  m:l10n/setLocale
         * @param   string      $locale         Localization string in the form of language_COUNTRY (e.g.: de_DE, en_US, ...).
         * @return  string                      Returns old localisation setting.
         */
        public function setLocale($locale)
        /**/
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

        /**
         * Get current localisation setting.
         *
         * @octdoc  m:l10n/getLocale
         * @return  string                      Current localization setting in the form of language_COUNTRY (e.g.: de_DE, en_US, ...).
         */
        public function getLocale()
        /**/
        {
            return $this->lc;
        }

        /**
         * Return language code from current set locale or from specified locale.
         *
         * @octdoc  m:l10n/getLanguageCode
         * @param   string      $code           Optional code to parse.
         * @return  string                      Language code.
         */
        public function getLanguageCode($code = null)
        /**/
        {
            $parts = explode('_', (is_null($code) ? $this->lc : $code));

            return strtolower($parts[0]);
        }

        /**
         * Return country code from current set locale or form specified locale.
         *
         * @octdoc  m:l10n/getCountryCode
         * @param   string      $code           Optional code to parse.
         * @return  string                      Country code.
         */
        public function getCountryCode($code = null)
        /**/
        {
            $parts = explode('_', (is_null($code) ? $this->lc : $code));

            return strtoupper(array_pop($parts));
        }

        /**
         * One level restoring locale setting, when a setting was overwritten using setLocale.
         *
         * @octdoc  m:l10n/restoreLocale
         */
        public function restoreLocale()
        /**/
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
                return function($obj, $args) use ($txt) { return $txt; };
            } else {
                return create_function('$obj, $args', 'return ' . $txt . ';');
            }
        }
    }
}

/*
 * put translate function into global namespace
 */
namespace {
    /****f* l10n/translate
     * SYNOPSIS
     */
    function translate()
    /*
     * FUNCTION
     *      global translate function
     * INPUTS
     *      * $txt (string) -- text to lookup in dictionary
     *      * ... (mixed) -- additional optional parameters for embedded functions
     * OUTPUTS
     *      (string) -- text from dictionary or txt, if text was not found in dictionary
     ****
     */
    {
        return \org\octris\core\l10n::getInstance()->gettext(func_get_args());
    }
}
