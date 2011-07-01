<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core {
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
         * Gettext compiler cache -- an array -- is only used, if a caching backend is not set.
         *
         * @octdoc  v:l10n/$compiler_cache
         * @var     array
         * @see     l10n::setCache
         */
        protected $compiler_cache = array();
        /**/

        /**
         * L10n caching backend.
         *
         * @octdoc  v:l10n/$cache
         * @var     \org\octris\core\cache
         */
        protected static $cache = null;
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
         * Set caching backend for l10n.
         *
         * @octdoc  m:l10n/setCache
         * @param   \org\octris\core\cache      $cache          Instance of caching backend to use.
         */
        public static function setCache(\org\octris\core\cache $cache)
        /**/
        {
            self::$cache = $cache;
        }

        /**
         * Return instance of caching backend.
         *
         * @octdoc  m:l10n/getCache
         * @return  \org\octris\core\cache                      Instance of caching backend l10n uses.
         */
        public static function getCache()
        /**/
        {
            return self::$cache;
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
            if (($pos = strpos($locale, '.')) !== false) {
                $locale = substr($locale, 0, $pos);
            }
            
            $ret      = $this->lc;
            $this->lc = $locale;

            array_push($this->lc_mem, $ret);

            // putenv('LANG=' . $locale);
            // putenv('LC_MESSAGES=' . $locale);
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

        /**
         * Money formatter.
         *
         * @octdoc  m:l10n/monf
         * @todo    implementation
         * @param   mixed           $money              Float value as amount or instance of \org\octris\core\type\money
         * @return  string                              Formatted money.
         */
        public function monf($money)
        /**/
        {
        }

        /**
         * Number formatter.
         *
         * @octdoc  m:l10n/numf
         * @todo    implementation
         * @param   mixed           $number             Numerical value to format.
         * @return  string                              Formatted number.
         */
        public function numf($number)
        /**/
        {
        }

        /**
         * Date formatter. Can either be an ISO date string, a timestamp or
         * a PHP DateTime object.
         *
         * @octdoc  m:l10n/datef
         * @todo    Implementation.
         * @param   mixed           $datetime           Date.
         * @param   int             $format             Optional formatting type.
         * @return  string                              Formatted date.
         */
        public function datef($datetime, $format)
        /**/
        {
        }

        /**
         * If parameter 'test' ist bool true, the parameter 'first' will
         * be returnes, otherwise the parameter 'second' will be returned.
         *
         * @octdoc  m:l10n/yesno
         * @param   mixed           $test               Value to test.
         * @param   string          $first              First possible return value.
         * @param   string          $second             Second possible return value.
         * @return  string                              Return value according to 'test'.
         */
        public function yesno($test, $first, $second = '')
        /**/
        {
            return (!!$test ? $first : $second);
        }

        /**
         * Quantisation. The string parameters 'first', 'second' and 'third'
         * may contain a %d placeholder (@see sprintf) to include the value
         * of 'test'.
         *
         * @octdoc  m:l10n/quant
         * @param   int/float       $test               Value to test.
         * @param   string          $first              Return value if 'test' == 1 or 'second' / 'third' are not set.
         * @param   string          $second             Optional return value if 'test' != 1.
         * @param   string          $third              Optional return value if 'test' == 0.
         * @return  string                              Return value according to 'test'.
         */
        public function quant($test, $first, $second = null, $third = null)
        /**/
        {
            $return = $first;

            if ($test == 0 && !is_null($third)) {
                $return = $third;
            } elseif ($val != 1 && !is_null($second)) {
                $return = $second;
            }

            return \org\octris\core\type\string::sprintf($return, $val);
        }

        /**
         * Writes out a list of values separated by a specified character
         * (default: ', ') and the last one by a string (eg: 'and' or 'or').
         *
         * @octdoc  m:l10n/comify
         * @param   array           $list               List of elements to concatenate.
         * @param   string          $word               Word to concatenate last item with.
         * @param   string          $sep                Optional separator.
         * @return  string                              Concatenated list.
         */
        public function comify(array $list, $word, $sep = ', ')
        /**/
        {
            $return = '';

            if (count($list) > 0) {
                $last = array_pop($list);

                $return = implode($word, array(implode($sep, $list), $last));
            }

            return $return;
        }

        /**
         * Returns text according to specified gender.
         *
         * @octdoc  m:l10n/gender
         * @param   int/string      $gender             Gender (one of: mM1fFwW2nN0)
         * @param   string          $undefined          String to return if gender is not specified ('gender' one of 'n', 'N' or '0').
         * @param   string          $male               String to return if gender is male ('gender' one of 'm', 'M' or '1').
         * @param   string          $female             String to return if gender is female ('gender' one of 'f', 'F' or '2').
         * @return  string                              String according to specified gender.
         */
        public function gender($gender, $undefined, $male, $female)
        /**/
        {
            switch (strtoupper($gender)) {
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

        /**
         * Bind gettext text domain, the package and directory of locale texts).
         *
         * @octdoc  m:l10n/bindTextDomain
         * @param   string          $pkg                Name of package
         * @param   string          $dir                Base directory for localized text packages.
         * @param   string          $codeset            Optional codeset of text domain.
         * @return  string                              Text domain.
         */
        protected function bindTextDomain($pkg, $dir, $codeset = 'UTF-8')
        /**/
        {
            bind_textdomain_codeset($pkg, $codeset);
            $domain = bindtextdomain($pkg, $dir);

            textdomain($pkg);

            return $domain;
        }

        /**
         * Alias for '_'
         *
         * @octdoc  m:l10n/gettext
         * @param   string          $msg                Message to lookup in directory.
         * @param   string          ...                 Optional parameters for embedding into message.
         * @return  string                              Translated text or text from 'msg' parameter, if no translation was found.
         */
        public function gettext()
        /**/
        {
            $this->_(func_get_args());
        }

        /**
         * Translate message with currently set dictionary.
         *
         * @octdoc  m:l10n/_
         * @param   string          $msg                Message to lookup in directory.
         * @param   string          ...                 Optional parameters for embedding into message.
         * @return  string                              Translated text or text from 'msg' parameter, if no translation was found.
         */
        public function _()
        /**/
        {
            $args = func_get_args();

            if (is_array($args[0])) $args = $args[0];

            $msg = (string)array_shift($args);

            // get localized text from dictionary
            if ($msg !== '') {
                $msg = $this->translate($msg);
            }

            // compile included function calls if not in cache
            if (!is_null(self::$cache)) {
                $cache = self::$cache;
            } else {
                $cache =& $this->compiler_cache;
            }
            
            $key = $this->lc . '.' . $msg;
            
            if (!isset($cache[$key])) {
                $cache[$key] = $this->compile($msg);
            }

            return $cache[$key]($this, $args);
        }

        /**
         * Lookup a message in the dictionary and return it's translation.
         * This method differs from '_' and 'gettext' in that it won't
         * compile any inline functions.
         *
         * @octdoc  m:l10n/translate
         * @param   string          $msg                Message to lookup
         * @return  string                              Translated message.
         */
        public function translate($msg)
        /**/
        {
            return ($msg !== '' && (($out = gettext($msg)) !== '') 
                    ? $out 
                    : $msg);
        }

        /**
         * Gettext message compiler. It's purpose is to transform embedded
         * functions into PHP code.
         *
         * @octdoc  m:l10n/compile
         * @param   string          $msg                Message to compile.
         * @return  callback                            Created callback.
         */
        protected function compile($msg)
        /**/
        {
            $msg     = '\'' . str_replace("'", "\'", $msg) . '\'';
            $pattern = '/\[(?:(_\d+)|(?:([^,]+))(?:,(.*?))?(?<!\\\))\]/s';

            $msg = preg_replace_callback($pattern, function($m) {
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
            }, $msg, -1, $cnt = 0);

            if ($cnt == 0) {
                return function($obj, $args) use ($msg) { return $msg; };
            } else {
                return create_function('$obj, $args', 'return ' . $msg . ';');
            }
        }
    }
}

/*
 * put translate function into global namespace
 */
namespace {
    /**
     * Global translate function.
     *
     * @octdoc  m:l10n/translate
     * @param   string      $txt            Text to lookup dictionary.
     * @param   mixed       $arg, ...       Optional additional arguments.
     * @return  string                      Localized text.
     */
    function translate()
    /**/
    {
        return \org\octris\core\l10n::getInstance()->_(func_get_args());
    }
}
