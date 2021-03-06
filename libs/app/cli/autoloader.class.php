<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\app\cli {
    use \org\octris\core\validate as validate;
    use \org\octris\core\provider as provider;

    /**
     * Class Autoloader. This is a special autoloader for cli applications. It's required because they may
     * have libraries installed in a libs subdirectory of the clis' directory located in the tools directory.
     *
     * @octdoc      c:cli/autoloader
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class autoloader
    /**/
    {
        /**
         * Class Autoloader.
         *
         * @octdoc  m:app/autoload
         * @param   string      $classpath      Path of class to load.
         */
        public static function autoload($classpath)
        /**/
        {
            $env = provider::access('env');

            $pkg = preg_replace('|\\\\|', '/', preg_replace('|\\\\|', '.', ltrim($classpath, '\\\\'), 2)) . '.class.php';

            $pkg = $env->getValue('OCTRIS_BASE', validate::T_PATH) . '/tools/' . $pkg;

            try {
                include_once($pkg);
            } catch(\Exception $e) {
            }
        }
    }

    spl_autoload_register(array('\org\octris\core\app\cli\autoloader', 'autoload'));
}
