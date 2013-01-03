<?php

/*
 * This file is part of the '{{$directory}}' package.
 *
 * (c) {{$author}} <{{$email}}>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace {{$namespace}}\libs {
    /**
     * Class Autoloader.
     *
     * @octdoc      c:libs/autoloader
     * @copyright   copyright (c) {{$year}} by {{$company}}
     * @author      {{$author}} <{{$email}}>
     */
    class autoloader
    /**/
    {
		/**
		 * Whether script is executed from within a phar.
		 *
		 * @octdoc 	m:autoloader/$is_phar
		 * @var 	bool
		 */
		protected static $is_phar = false;
		/**/
	
		/**
		 * Set whether script is executed from within a phar.
		 *
		 * @octdoc 	m:autoloader/setIsPhar
		 * @param 	bool 		$is_phar 		Whether script is executed from within a phar.
		 */
		public static function setIsPhar($is_phar)
		/**/
		{
			self::$is_phar = $is_phar;
		}
	
        /**
         * Class Autoloader.
         *
         * @octdoc  m:autoloader/autoload
         * @param   string      $classpath      Path of class to load.
         */
        public static function autoload($classpath)
        /**/
        {
			$deps = ((self::$is_phar && strpos($class, '{{$namespace}}') !== 0)
					 ? __DIR__ . '../deps/'
					 : '');
	
            $pkg = preg_replace('|\\\\|', '/', preg_replace('|\\\\|', '.', ltrim($classpath, '\\\\'), 2)) . '.class.php';

            try {
                include_once($deps . $pkg);
            } catch(\Exception $e) {
            }
        }
    }

    spl_autoload_register(array('{{$namespace}}\autoloader', 'autoload'));
}
