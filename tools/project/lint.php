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
     * Project lint tool. Checks the following files:
     * 
     * * PHP Linter
     * * PHP Check, if any UTF-8 unsafe functions are used.
     * * Templates (using tpl\lint)
     * * JavaScript lint?
     *
     * @octdoc      h:project/lint
     * @copyright   copyright (c) 2012 by Harald Lapp
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
    app\main::getInstance()->invoke(new app\create());
}
