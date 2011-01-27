<?php

namespace org\octris\core\app\cli {
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
            $path = $_ENV['OCTRIS_BASE']->value . '/tools/';
            
            $pkg = $path . preg_replace('|\\\\|', '/', preg_replace('|\\\\|', '.', ltrim($classpath, '\\\\'), 2)) . '.class.php';

            @include_once($pkg);
        }
    }

    spl_autoload_register(array('\org\octris\core\app\cli\autoloader', 'autoload'));
}
