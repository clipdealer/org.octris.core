#!/usr/bin/env php
<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\project {
    /**
     * This tool wraps the xgettext tool provided by the gettext tools
     * to support gettext translation of applications developed using 
     * the octris framework and support for translating templates.
     *
     * @octdoc      h:project/xgettext
     * @copyright   copyright (c) 2014 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    /**/

    $_ENV['OCTRIS_APP'] = 'org.octris.core';

    // include core cli application library
    require_once('org.octris.core/app/cli.class.php');
    
    // load application configuration
    $registry = \org\octris\core\registry::getInstance();
    $registry->set('config', function() {
        return new \org\octris\core\config('org.octris.core');
    }, \org\octris\core\registry::T_SHARED | \org\octris\core\registry::T_READONLY);

    // run application
    app\main::getInstance()->invoke(new app\xgettext());
}
